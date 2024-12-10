<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use LdapRecord\Container;
use LdapRecord\Models\ActiveDirectory\User as LdapUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    // Menampilkan form login
    public function showLoginForm()
    {
        return view('login');
    }

    // Proses login
    public function login(Request $request)
    {
        // $adServer = "ldap://gysdc01.gyssteel.com ldap://gysdc02.gyssteel.com";

        // $ldap = ldap_connect($adServer);
        // $username = $request->username;
        // $password = $request->password;

        // $ldaprdn = 'gys' . "\\" . $username;

        // ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        // ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

        // $bind = @ldap_bind($ldap, $ldaprdn, $password);

        // $query = DB::select("SELECT * FROM mst_users WHERE is_active = 0 AND username = ?", [$username]);
        // $data = $query[0];

        // if ($bind) {
        //     $filter = "(sAMAccountName=$username)";
        //     $result = ldap_search($ldap, "dc=gyssteel,dc=com", $filter);
        //     // ldap_sort($ldap,$result,"sn");
        //     $info = ldap_get_entries($ldap, $result);
        //     for ($i = 0; $i < $info["count"]; $i++) {
        //         if ($info['count'] > 1)
        //             break;
        //         $str = $info[$i]["memberof"][2] ?? NULL;
        //         $str1 = (explode(",", $str));
        //         $group = (explode("=", $str1[0]));
        //         //echo $str2[1];
        //         $_SESSION['GROUP'] = $group[1] ?? NULL;
        //         $_SESSION['nama'] = $info[$i]["givenname"][0];
        //         $_SESSION['ext'] = $info[$i]["telephonenumber"][0];
        //         $_SESSION['email'] = $info[$i]["mail"][0];
        //         $_SESSION['start'] = time(); // taking now logged in time
        //         $timeout = 300;
        //         $_SESSION['expire'] = $_SESSION['start'] + $timeout;

        //         $userDn = $info[$i]["distinguishedname"][0];

        //         $query = DB::select("SELECT * FROM mst_users WHERE is_active = 0 AND username = ?", [$username]);
        //         $data = $query[0];

        //         if (empty($data->username)) {
        //             exit("<script>window.alert('Kamu tidak memiliki akses!');window.history.back();</script>");
        //             redirect("login");
        //         } else {
        //             $request->session()->regenerate();
        //             return redirect()->route('register');
        //         }
        //     }
        //     @ldap_close($ldap);
        // } elseif (!empty($data->username && password_verify($password, $data->password))) {
        // }
        // $data = $request->only('username', 'password');

        // if (Auth::attempt($data)) {
        //     $request->session()->regenerate();
        //     return redirect()->route('register');
        // } else {
        //     return redirect()->back()->with('gagal', 'Username atau Password salah');
        // }


        // if (password_verify($password, $data->password)) {
        //     $infoUser = array("admin" => "true", "type" => $data->usr_access, "nama" => $data->display_name, "email" => $data->email, "id" => $data->id);

        //     $request->session()->put($infoUser);

        //     // Regenerasi session untuk keamanan
        //     $request->session()->regenerate();
        //     return redirect()->route('register');
        // }

        $adServers = ["ldap://gysdc01.gyssteel.com", "ldap://gysdc02.gyssteel.com"];
        $username = $request->username;
        $password = $request->password;

        $ldapConnected = false;
        $ldapConnection = null;

        // Coba koneksi ke salah satu server LDAP
        foreach ($adServers as $adServer) {
            $ldapConnection = @ldap_connect($adServer);

            if ($ldapConnection) {
                ldap_set_option($ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3);
                ldap_set_option($ldapConnection, LDAP_OPT_REFERRALS, 0);

                $ldapRdn = "gys\\" . $username;
                $bind = @ldap_bind($ldapConnection, $ldapRdn, $password);

                if ($bind) {
                    $ldapConnected = true;
                    break;
                } else {
                    print_r('gagal');
                }
            }
        }

        $query = DB::table('mst_users')
            ->where('is_active', 0)
            ->where('username', $username)
            ->first();

        if ($ldapConnected) {
            $filter = "(sAMAccountName=$username)";
            $result = @ldap_search($ldapConnection, "dc=gyssteel,dc=com", $filter);

            if ($result) {
                $info = @ldap_get_entries($ldapConnection, $result);

                if ($info["count"] > 0) {
                    $group = $info[0]["memberof"][2] ?? null;
                    $groupParts = $group ? explode(",", $group) : [];
                    $groupName = $groupParts ? explode("=", $groupParts[0])[1] ?? null : null;

                    $request->session()->put([
                        'GROUP' => $groupName,
                        'nama' => $info[0]["givenname"][0] ?? null,
                        'ext' => $info[0]["telephonenumber"][0] ?? null,
                        'email' => $info[0]["mail"][0] ?? null,
                        'start' => time(),
                        'expire' => time() + 300,
                    ]);

                    if (!$query) {
                        return back()->with('error', 'Kamu tidak memiliki akses!');
                    }

                    $infoUser = array("admin" => "true", "type" => $query->usr_access, "nama" => $query->display_name, "email" => $query->email, "id" => $query->id);
                    $request->session()->put($infoUser);
                    return redirect()->route('register');
                }
            }

            @ldap_close($ldapConnection);
        }

        if ($query && password_verify($password, $query->password)) {
            $infoUser = array("admin" => "true", "type" => $query->usr_access, "nama" => $query->display_name, "email" => $query->email, "id" => $query->id);
            $request->session()->put($infoUser);
            return redirect()->route('register');
        }

        return back()->withErrors(['message' => 'Username atau password salah!']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('login');
    }
}
