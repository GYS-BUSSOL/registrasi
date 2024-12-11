@extends('layout')

@section('title', 'Undian')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <div class="page-title">
        <h3>Undian</h3>
    </div>


    <div class="card">
        <div class="card-header">
            <div class="col-12">
                <div class="row">
                    <div class="text-left"><br>
                        <hr>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <h1 class="text-center mb-4">Undian Peserta</h1>

                <!-- Display Random Number -->
                <div id="random-number" class="display-3 text-primary text-center fw-bold">000000000</div>

                <!-- Display Result -->
                <div id="result" class="mt-4 text-center"></div>

                <!-- Buttons -->
                <div class="text-center mt-4">
                    <button id="start-button" class="btn btn-primary btn-lg me-2" onclick="startDraw()">Mulai
                        Undian</button>
                    <button id="stop-button" class="btn btn-danger btn-lg" onclick="stopDraw()" disabled>Berhenti</button>
                </div>
            </div>
        </div>
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
                    resultElement.innerHTML = `
                        <div class="alert alert-success mt-3">
                            <h3>${response.data.message}</h3>
                            <p>ID Karyawan: <strong>${winner.employee_id}</strong></p>
                            <p>Pemenang: <strong>${winner.full_name}</strong></p>
                            <p>Department: <strong>${winner.department_name}</strong></p>
                        </div>
                    `;
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

@endsection
