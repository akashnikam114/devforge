@extends('admin.layouts.app')
@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Users</h3>
                    <div class="nk-block-des">
                        <p>List of all application users</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="nk-block nk-block-lg mt-3">
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <table class="table" id="myTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            NioApp.DataTable('#myTable', {
                processing: true,
                serverSide: true,
                searching: true,
                bLengthChange: true,
                ajax: "{{ route('admin.users') }}",
                order: [[0, "desc"]],
                responsive: !0,
                lengthMenu: [[10, 100, 500, -1], [10, 100, 500, "All"]],
                columns: [
                    { data: "name" },
                    { data: "email", render: function(data) { return data || 'NA'; } },
                    { data: "phone_number", render: function(data) { return data || 'NA'; } },
                    { data: "role_name" },
                    { data: "status" },
                    {
                        data: "created_at",
                        render: function(data) {
                            return data ? moment(data).format('DD-MM-YYYY HH:mm A') : 'NA';
                        }
                    },
                    { data: "action", orderable: false, searchable: false }
                ]
            });
        });
    </script>
@endsection
