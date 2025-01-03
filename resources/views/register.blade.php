@can('access-admin-or-hr')
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
                            <form id="barcode" class="form-horizontal">
                                @csrf
                                <div class="card-body">
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
                                            {{-- </form> --}}
                                        </div> <br>
                                        <div class="row">
                                            {{-- <form id="barcode" action="">
                                        @csrf --}}
                                            <div class="form-group col-md-4" style="margin-left: 10px;">
                                                <input type="text" class="form-control" name="qr_number" id='qr_number'
                                                    placeholder="Masukan NIK Karyawan" required />
                                            </div>
                                            <div>
                                                <button type="submit" class="btn btn-primary">Search</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
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
                            <p>Ukuran Baju: <strong><span id="size"></span></strong></p>
                            <p>Group: <strong><span id="group"></span></strong></p>
                            <p>Warna Group: <strong><span id="warna-group"></span></strong></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary ml-1" data-bs-dismiss="modal">
                        <span class="d-sm-block">Batal</span>
                    </button>
                    <button id="confirm" type="submit" class="btn btn-primary ml-1" data-bs-dismiss="modal">
                        <span class="d-sm-block">OK</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Popup Karyawan sudah Registrasi dan Karyawan tidak ditemukan-->
    <div class="modal fade text-left" id="popUpInfo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title white" id="myModalLabel120">
                        Info
                    </h5>
                </div>
                <div class="modal-body" id="info">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary ml-1" data-bs-dismiss="modal">
                        <span class="d-sm-block">OK</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Content Ends-->

    <!-- JS SCAN CODE QR -->
    <script src="{{ asset('assets/js/html5-qrcode.min.js') }}"></script>
    <!-- JS SCAN CODE QR -->

    <script type="text/javascript">
        $(document).ready(function() {
            $('#barcode').on('submit', function(e) {
                e.preventDefault();
                let idKaryawan = $('#qr_number').val();
                getData(idKaryawan)
            });
        });

        const html5QrCode = new Html5Qrcode("reader");

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
                    getData(qrCodeMessage);
                }
                html5QrcodeScanner.clear();
            }

            Html5Qrcode.getCameras()
                .then(cameras => {
                    if (cameras && cameras.length) {
                        console.log(cameras)
                        const cameraId = cameras[Object.keys(cameras).length - 1].id;
                        html5QrCode.start(
                            cameraId, {
                                fps: 10,
                                qrbox: {
                                    width: 250,
                                    height: 250
                                }
                            },
                            onScanSuccess, // Callback untuk pemindaian sukses
                            (errorMessage) => {
                                console.warn(`QR Code scan failed: ${errorMessage}`);
                            }
                        ).catch(err => console.error("Failed to start scanning:", err));
                    } else {
                        console.error("No cameras found.");
                    }
                })
                .catch(err => console.error("Error getting cameras:", err));

        });



        function getData(qrCode) {
            console.log(qrCode)
            const info = document.getElementById('info');
            let csrfToken = $('input[name="_token"]').val();

            $.ajax({
                url: '/cek-karyawan', // Route ke controller
                method: 'POST',
                data: {
                    _token: csrfToken,
                    employee_id: qrCode,
                    scan: 1,
                },
                success: function(response) {
                    if (response.success) {
                        // Tampilkan data di modal
                        $('#id').text(response.data.id);
                        $('#nama').text(response.data.nama);
                        $('#department').text(response.data.department);
                        $('#size').text(response.data.size);
                        $('#group').text(response.data.group);
                        $('#warna-group').text(response.data.warna_group);
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
                                    info.innerHTML = `
                                    <p>${saveResponse.message}</p>
                                    `;
                                    // Tampilkan modal
                                    $('#popUpInfo').modal('show');
                                    $('#popUpInfo').on('hidden.bs.modal', function() {});
                                }
                            });
                        });
                    } else {
                        // Jika karyawan sudah registrasi atau karyawan tidak ditemukan tampilkan popup
                        info.innerHTML = `
                                    <p>${response.data.message}</p>
                                    <p>${response.data.nama} - ${response.data.department}</p>
                                    `;
                        // Tampilkan modal
                        $('#popUpInfo').modal('show');
                        $('#popUpInfo').on('hidden.bs.modal', function() {});
                    }
                }
            });
        }
    </script>
@endsection
@endcan
