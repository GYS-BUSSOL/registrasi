@can('access-admin-or-hr')
    @extends('layout')

    @section('title', 'List User')

@section('content')

    <div class="page-title">
        <h3>Registrasi</h3>
    </div>


    <div class="card">
        <div class="card-header">
            <div class="col-12">
                <div class="row">
                    <div class="text-left">Data Registrasi Employee Day<br>
                        <hr>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive">
                <table class='table table-striped' id="list">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>ID Karyawan</th>
                            <th>Nama</th>
                            <th>Department</th>
                            <th>Tanggal & Waktu</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i = 1; @endphp
                        @foreach ($users as $user)
                            <tr>
                                <td>@php
                                    echo $i;
                                @endphp</td>
                                <td>{{ $user->employee_id }}</td>
                                <td>{{ $user->full_name }}</td>
                                <td>{{ $user->department_name }}</td>
                                <td>{{ $user->updated_at }}</td>
                                <td>
                                    @if ($user->is_flag == 1)
                                        Registrasi
                                    @else
                                        Lunch
                                    @endif
                                </td>
                                @php
                                    $i++;
                                @endphp
                        @endforeach
                        </tr>
                        @php $i++; @endphp

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#list').DataTable({
                dom: 'Blfrtip',
                buttons: [{
                    extend: 'excel',
                    title: 'Data Registrasi Employee Day',
                    text: 'Export data ke Excel',
                    exportOptions: {
                        columns: [':visible'], // Ekspor hanya kolom yang terlihat
                    },
                }, ],
                responsive: true, // Tambahkan jika tabel perlu responsif
            });
        });
    </script>

@endsection
@endcan
