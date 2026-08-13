@extends('admin.layouts.app')
@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Restriction Setting</h3>
                    <div class="nk-block-des">
                        <p>List of all restriction settings</p>
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
                                <th>Restriction Name</th>
                                <th>Restriction Status</th>
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
            datatable1();
        });

        function datatable1() {
            NioApp.DataTable('#myTable', {
                "processing": true,
                "serverSide": true,
                "searching": true,
                "bLengthChange": true,
                ajax: "{{ route('admin.restriction_settings') }}",
                "order": [[0, "desc"]],
                responsive: !0,
                autoFill: !0,
                keys: !0,
                lengthMenu: [
                    [10, 100, 500, -1],
                    [10, 100, 500, "All"]
                ],
                "columns": [
                    {
                        "mData": "restriction_name"
                    },
                    {
                        "mData": "is_restriction_enabled",
                        "mRender": function(data) {
                            const status = (data == 1) ? { class: 'success', text: 'Enabled' } : { class: 'danger', text: 'Disabled' };
                            return `<h6 class='text-${status.class} sub-text' style='margin-top:5px;'>${status.text}</h6>`;
                        }
                    },
                    {
                        "mData": "action"
                    }
                ]
            });
        }
    </script>
@endsection
