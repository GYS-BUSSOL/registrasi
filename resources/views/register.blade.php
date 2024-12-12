@extends('layout')

@section('title', 'Scan Registrasi')

@section('content')

    <div class="content-wrapper">
        <section class="content-header">
            <h3> Scan QR Registration</h3>
        </section>
        <section class="content">
            <!-- Default box -->
            <div class="row clearfix">
                <div class="col-md-12">
                    <div class="panel panel-primary cardbg">
                        <div class="card">
                            <div class="card-body">
                                <form id="barcode" class="form-horizontal">
                                    @csrf
                                    <div class="col-md-12"><br>
                                        <div>
                                            <!-- SCAN CODE QR WITH CAMERA -->
                                            <div class="col-md-5 col-md-offset-4">
                                                <div class="panel panel-warning">
                                                    <div id="reader"></div>
                                                    <div id="qr-reader-results"></div>
                                                </div>
                                            </div>
                                            <!-- SCAN CODE QR WITH CAMERA -->
                                        </div> <br>
                                        <div class="row">
                                            <div class="form-group col-md-4" style="margin-left: 10px;">
                                                <input type="hidden" name="scan" id="scan" value="1">
                                                <input type="text" class="form-control" name="qr_number" id='qr_number'
                                                    placeholder="Masukan Nomor Barcode" required />
                                            </div>
                                            <div>
                                                <button type="submit" class="btn btn-primary">Search</button>
                                            </div>
                                        </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </section>
    </div>

    <!-- Modal Popup Konfirmasi data karyawan-->
    <div class="modal fade text-left" id="popUp" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title white" id="myModalLabel120">
                        Data Karyawan
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <ul>
                                <p>Nama: <strong><span id="nama"></span></strong> </p>
                                <p>ID Karyawan: <strong><span id="id"></span></strong></p>
                                <p>Department: <strong><span id="department"></span></strong> </p>
                            </ul>
                        </div>
                        <div class="col-6">
                            <img src="{{ asset('assets/images/avatar/avatar-s-1.png') }}" alt="Foto Karyawan">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="confirm" type="submit" class="btn btn-primary ml-1" data-bs-dismiss="modal">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">OK</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Popup Karyawan sudah Registrasi dan Karyawan tidak ditemukan-->
    <div class="modal fade text-left" id="popUpFail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title white" id="myModalLabel120">
                        Info
                    </h5>
                </div>
                <div class="modal-body">
                    <p><span id="message"></span></p>
                    <p><span id="fail-nama"></span> - <span id="fail-department"></span></p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary ml-1" data-bs-dismiss="modal">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">OK</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Content Ends-->

    {{-- Check Karyawan --}}
    <script>
        $(document).ready(function() {
            $('#barcode').on('submit', function(e) {
                e.preventDefault();

                let scan = $('#scan').val();
                let idKaryawan = $('#qr_number').val();
                let csrfToken = $('input[name="_token"]').val();

                // Kirim data ke controller menggunakan AJAX
                $.ajax({
                    url: '/cek-karyawan', // Route ke controller
                    method: 'POST',
                    data: {
                        _token: csrfToken,
                        employee_id: idKaryawan,
                        scan: scan,
                    },
                    success: function(response) {
                        if (response.success) {
                            // Tampilkan data di modal
                            $('#id').text(response.data.id);
                            $('#nama').text(response.data.nama);
                            $('#department').text(response.data.department);
                            // Tampilkan modal
                            $('#popUp').modal('show');


                            // Konfirmasi untuk menyimpan data
                            $('#confirm').on('click', function() {
                                $.ajax({
                                    url: '/', // Route untuk menyimpan ke tabel register
                                    method: 'POST',
                                    data: {
                                        _token: csrfToken,
                                        employee_id: response.data.id
                                    },
                                    success: function(saveResponse) {
                                        alert(saveResponse.message);
                                        location
                                            .reload(); // Muat ulang halaman jika perlu
                                    }
                                });
                            });
                        } else {
                            // Jika karyawan sudah registrasi atau karyawan tidak ditemukan tampilkan popup
                            $('#message').text(response.data.message);
                            $('#fail-nama').text(response.data.nama);
                            $('#fail-department').text(response.data.department);
                            // Tampilkan modal
                            $('#popUpFail').modal('show');
                        }
                    }
                });
            });
        });
    </script>

    <!-- JS SCAN CODE QR -->
    <script src="{{ asset('assets/js/html5-qrcode.min.js') }}"></script>
    <!-- JS SCAN CODE QR -->

    <script type="text/javascript">
        // JQUERY SCAN CODE QR

        function docReady(fn) {
            // see if DOM is already available
            if (document.readyState === "complete" || document.readyState === "interactive") {
                // call on next available tick
                setTimeout(fn, 1);
            } else {
                document.addEventListener("DOMContentLoaded", fn);
            }
        }

        docReady(function() {
            var resultContainer = document.getElementById('qr-reader-results');
            var lastResult, countResults = 0;

            function onScanSuccess(qrCodeMessage) {
                if (qrCodeMessage !== lastResult) {
                    ++countResults;
                    lastResult = qrCodeMessage;
                    resultContainer.innerHTML += `${qrCodeMessage}'`;
                    getData(resultContainer.innerHTML);
                }
                html5QrcodeScanner.clear();
            }


            var html5QrcodeScanner = new Html5QrcodeScanner(
                "reader", {
                    fps: 10,
                    qrbox: 250
                });
            html5QrcodeScanner.render(onScanSuccess);

        });
        // JQUERY SCAN CODE QR
        function getData(v) {
            url = v;
            // console.log(url);
            location.href = url;
        }
    </script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script> --}}

@endsection
