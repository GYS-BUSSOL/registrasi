<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/vendors/chartjs/Chart.min.css') }}">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

    <link rel="stylesheet" href="{{ asset('assets/vendors/datatables/media/css/jquery.dataTables.css') }}">

    <script src="{{ asset('assets/vendors/datatables/media/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/datatables/media/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/datatables/media/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/datatables/media/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/datatables/media/js/jszip.min.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('assets/vendors/choices.js/choices.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/GYSLogo.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('assets/font-awesome/css/font-awesome.min.css') }}">

    <link href="{{ asset('assets/bootstrap-toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <div class="d-flex align-items-center justify-content-center text-white"
        style="background: url({{ asset('assets/images/background/auth1.jpg') }}) center/cover no-repeat; height: 100vh;">
        <div class="container">
            <div class="row">
                <div class="col-md-5 col-sm-12 mx-auto">
                    <div class="card pt-4">
                        <div class="card-body">
                            <div class="text-center mb-5">
                                <div class="card-body">
                                    <!-- Display Random Number -->
                                    <div id="random-number" class="display-3 text-primary text-center fw-bold">000000000
                                    </div>

                                    <!-- Display Result -->
                                    <div id="result" class="mt-4 text-center"></div>

                                    <!-- Buttons -->
                                    <div class="text-center mt-4">
                                        <button id="start-button" class="btn btn-primary btn-lg me-2"
                                            onclick="startDraw()">Mulai
                                            Undian</button>
                                        <button id="stop-button" class="btn btn-danger btn-lg" onclick="stopDraw()"
                                            disabled>Berhenti</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- <div class="card">
            <div class="card-header">
                <div class="col-12">
                    <div class="row">
                        <div class="text-left"><br>
                            <hr>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Display Random Number -->
                    <div id="random-number" class="display-3 text-primary text-center fw-bold">000000000</div>

                    <!-- Display Result -->
                    <div id="result" class="mt-4 text-center"></div>

                    <!-- Buttons -->
                    <div class="text-center mt-4">
                        <button id="start-button" class="btn btn-primary btn-lg me-2" onclick="startDraw()">Mulai
                            Undian</button>
                        <button id="stop-button" class="btn btn-danger btn-lg" onclick="stopDraw()"
                            disabled>Berhenti</button>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>

    <script>
        let participants = []; // Array untuk menyimpan ID karyawan
        let interval; // Interval untuk animasi pengacakan
        let isRunning = false; // Status animasi
        let currentRandomNumber = null; // Menyimpan nomor yang terakhir ditampilkan

        // Ambil data ID karyawan dari server
        function fetchParticipants() {
            return axios.get('/participants')
                .then(response => {
                    participants = response.data; // Simpan ID karyawan
                })
                .catch(error => {
                    console.error('Gagal mengambil data peserta:', error);
                });
        }
        // console.log(participants)

        // Mulai pengacakan
        function startDraw() {
            if (isRunning || participants.length === 0) return; // Jangan mulai jika sudah berjalan atau data kosong

            const randomNumberElement = document.getElementById('random-number');
            const resultElement = document.getElementById('result');
            resultElement.innerHTML = ''; // Bersihkan hasil sebelumnya

            isRunning = true;
            document.getElementById('start-button').disabled = true; // Nonaktifkan tombol Mulai
            document.getElementById('stop-button').disabled = false; // Aktifkan tombol Berhenti

            // Mulai animasi pengacakan angka
            interval = setInterval(() => {
                const randomIndex = Math.floor(Math.random() * participants.length);
                currentRandomNumber = participants[randomIndex]; // Simpan nomor terakhir
                randomNumberElement.textContent = currentRandomNumber; // Tampilkan ID karyawan acak
            }, 50); // Ubah angka setiap 50ms
        }

        // Hentikan pengacakan
        function stopDraw() {
            if (!isRunning) return; // Jangan berhenti jika tidak berjalan

            clearInterval(interval); // Hentikan animasi
            isRunning = false;
            document.getElementById('start-button').disabled = false; // Aktifkan tombol Mulai
            document.getElementById('stop-button').disabled = true; // Nonaktifkan tombol Berhenti

            const randomNumberElement = document.getElementById('random-number');
            const resultElement = document.getElementById('result');

            // Kirim permintaan ke server untuk mendapatkan pemenang
            axios.post('/draw', {
                    employee_id: currentRandomNumber
                })
                .then(response => {
                    const winner = response.data.winner;
                    randomNumberElement.textContent = winner.employee_id; // Tampilkan ID pemenang
                    if (response.data.status == 'success') {
                        resultElement.innerHTML = `
                        <div class="alert alert-success mt-3">
                            <h3>${response.data.message}</h3>
                            <p>Pemenang: <strong>${winner.full_name}</strong></p>
                            <p>ID Karyawan: <strong>${winner.employee_id}</strong></p>
                            <p>Department: <strong>${winner.department_name}</strong></p>
                        </div>
                    `;
                    } else {
                        resultElement.innerHTML = `
                        <div class="alert alert-danger mt-3">
                            <h3>${response.data.message}</h3>
                            <p>Pemenang: <strong>${winner.full_name}</strong></p>
                            <p>ID Karyawan: <strong>${winner.employee_id}</strong></p>
                            <p>Department: <strong>${winner.department_name}</strong></p>
                        </div>
                    `;
                    }

                })
                .catch(error => {
                    randomNumberElement.textContent = '000000000';
                    resultElement.innerHTML = `
                        <div class="alert alert-danger mt-3">
                            <p>${error.response.data.message}</p>
                        </div>
                    `;
                });
        }

        // Ambil data peserta saat halaman dimuat
        document.addEventListener('DOMContentLoaded', () => {
            fetchParticipants();
        });
    </script>
</body>

</html>
