<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    //Captcha
    public function reloadCaptcha()
    {
        return response()->json(['captcha' => captcha_img()]);
    }

    // Menampilkan form login
    public function showLoginForm()
    {
        return view('login');
    }

    // Proses login
    public function login(Request $request)
    {
        $request->validate([
            'captcha' => 'required|captcha'
        ], [
            'captcha.captcha' => 'Captcha tidak valid!'
        ]);
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
                }
            }
        }

        $query = DB::table('mst_users')->where('is_active', 0)->where('username', $username)->first();

        if (!$query) {
            return back()->with('error', 'Kamu tidak memiliki akses!');
        }

        if ($ldapConnected) {
            $filter = "(sAMAccountName=$username)";
            $result = @ldap_search($ldapConnection, "dc=gyssteel,dc=com", $filter);

            if ($result) {
                $info = @ldap_get_entries($ldapConnection, $result);

                if ($info["count"] > 0) {
                    // Mendapatkan group dan info pengguna dari LDAP
                    $group = $info[0]["memberof"][2] ?? null;
                    $groupParts = $group ? explode(",", $group) : [];
                    $groupName = $groupParts ? explode("=", $groupParts[0])[1] ?? null : null;

                    // Menyimpan informasi dalam session
                    $request->session()->put([
                        'GROUP' => $groupName,
                        'nama' => $info[0]["givenname"][0] ?? null,
                        'ext' => $info[0]["telephonenumber"][0] ?? null,
                        'email' => $info[0]["mail"][0] ?? null,
                        'start' => time(),
                        'expire' => time() + 300,
                    ]);

                    // Menyimpan informasi pengguna di session
                    $infoUser = array("admin" => "true", "type" => $query->usr_access, "nama" => $query->display_name, "email" => $query->email, "id" => $query->id);
                    $request->session()->put($infoUser);

                    // Login dengan Auth
                    Auth::loginUsingId($query->id); // Login dengan ID user

                    if ($query->usr_access == 'admin' || $query->usr_access == 'hr') {
                        return redirect()->route('register');
                    } else {
                        return redirect()->route('doorprize');
                    }
                }
            }

            @ldap_close($ldapConnection);
        }

        // Jika login dengan password di database
        if ($query && password_verify($password, $query->password)) {
            // Menyimpan informasi pengguna di session
            $infoUser = array("admin" => "true", "type" => $query->usr_access, "nama" => $query->display_name, "email" => $query->email, "id" => $query->id);
            $request->session()->put($infoUser);

            // Login dengan Auth
            Auth::loginUsingId($query->id); // Login dengan ID user

            if ($query->usr_access == 'admin' || $query->usr_access == 'hr') {
                return redirect()->route('register');
            } else {
                return redirect()->route('doorprize');
            }
        }

        return back()->with(['error' => 'Username atau password salah!']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('login');
    }
}
