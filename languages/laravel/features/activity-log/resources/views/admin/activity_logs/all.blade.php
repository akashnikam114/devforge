@extends('admin.layouts.app')
@push('styles')
    <style>
        .df-datetime-wrap {
            position: relative;
        }

        .df-datetime-input {
            cursor: pointer;
            background-color: #ffffff;
        }

        .df-datetime-popover {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 292px;
            padding: 14px;
            background: #ffffff;
            border: 1px solid #dbdfea;
            border-radius: 8px;
            box-shadow: 0 18px 44px rgba(31, 43, 58, 0.18);
            z-index: 1055;
            display: none;
        }

        .df-datetime-popover.show {
            display: block;
        }

        .df-datetime-head,
        .df-datetime-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .df-datetime-title {
            font-weight: 800;
            color: #364a63;
            text-align: center;
            flex: 1;
        }

        .df-datetime-nav {
            width: 32px;
            height: 32px;
            border: 1px solid #dbdfea;
            border-radius: 6px;
            background: #ffffff;
            color: #526484;
        }

        .df-datetime-week,
        .df-datetime-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
        }

        .df-datetime-week {
            margin-top: 12px;
            color: #8094ae;
            font-size: 11px;
            font-weight: 800;
            text-align: center;
            text-transform: uppercase;
        }

        .df-datetime-grid {
            margin-top: 6px;
        }

        .df-datetime-day {
            height: 34px;
            border: 0;
            border-radius: 6px;
            background: transparent;
            color: #364a63;
            font-weight: 700;
        }

        .df-datetime-day:hover,
        .df-datetime-day.selected {
            color: #ffffff;
            background: var(--app-primary);
        }

        .df-datetime-day.muted {
            color: #b7c2d0;
        }

        .df-datetime-time {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin: 12px 0;
        }

        .df-datetime-time input {
            height: 38px;
            text-align: center;
        }
    </style>
@endpush
@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Activity Logs</h3>
                    <div class="nk-block-des">
                        <p>List of all admin activity logs</p>
                    </div>
                </div>
                <div class="nk-block-head-content">
                    <ul class="nk-block-tools g-2">
                        <li style="width: 220px;">
                            <select class="form-select js-select2 filter" data-ui="lg" data-search="on" id="system_user_id" data-placeholder="Select System User">
                                <option value="all" selected disabled>Select System User</option>
                                @foreach($systemUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name ?: $user->email }}</option>
                                @endforeach
                            </select>
                        </li>
                        <li class="df-datetime-wrap" style="width: 220px;">
                            <input type="text" autocomplete="off" readonly class="form-control form-control-lg df-datetime-input filter" id="from_date_time" placeholder="Select From Date">
                        </li>
                        <li class="df-datetime-wrap" style="width: 220px;">
                            <input type="text" autocomplete="off" readonly class="form-control form-control-lg df-datetime-input filter" id="to_date_time" placeholder="Select To Date">
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="btn btn-lg btn-outline-danger" id="clearFilters">
                                <em class="icon ni ni-trash-alt"></em>
                                <span>Clear All</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="nk-block nk-block-lg mt-3">
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <table class="table" id="myTable">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Log</th>
                                <th>Event</th>
                                <th>Description</th>
                                <th>Properties</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        let activityLogTable;
        let activeDatePicker = null;

        $(document).ready(function() {
            setupDatePicker('#from_date_time');
            setupDatePicker('#to_date_time');
            datatable1();

            $('.filter').on('change', function() {
                if (activityLogTable) {
                    activityLogTable.ajax.reload();
                }
            });

            $('#clearFilters').on('click', function(event) {
                event.preventDefault();
                $('#system_user_id').val('all').trigger('change');
                $('#from_date_time').val('');
                $('#to_date_time').val('');
                if (activityLogTable) {
                    activityLogTable.ajax.reload();
                }
            });

            $(document).on('click', function(event) {
                if (!$(event.target).closest('.df-datetime-wrap').length) {
                    closeDatePickers();
                }
            });
        });

        function escapeHtml(value) {
            if (!value) return '';
            return String(value).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }

        function initActivityLogTooltips() {
            $('[data-bs-toggle="tooltip"]').each(function() {
                if (window.bootstrap && bootstrap.Tooltip) {
                    const existing = bootstrap.Tooltip.getInstance(this);
                    if (existing) {
                        existing.dispose();
                    }
                    new bootstrap.Tooltip(this, {
                        container: 'body',
                        html: false
                    });
                    return;
                }

                if ($.fn.tooltip) {
                    $(this).tooltip({
                        container: 'body'
                    });
                }
            });
        }

        function setupDatePicker(selector) {
            const input = document.querySelector(selector);
            if (!input) return;

            const wrap = input.closest('.df-datetime-wrap');
            const popover = document.createElement('div');
            popover.className = 'df-datetime-popover';
            wrap.appendChild(popover);

            const state = {
                view: new Date(),
                selected: null,
                hour: '00',
                minute: '00'
            };

            input.addEventListener('click', function(event) {
                event.stopPropagation();
                closeDatePickers(popover);
                activeDatePicker = popover;
                hydrateDatePickerState(input.value, state);
                renderDatePicker(input, popover, state);
                popover.classList.add('show');
            });
        }

        function hydrateDatePickerState(value, state) {
            if (!value) return;
            const parsed = moment(value, 'YYYY-MM-DD HH:mm', true);
            if (!parsed.isValid()) return;

            state.selected = parsed.toDate();
            state.view = parsed.toDate();
            state.hour = parsed.format('HH');
            state.minute = parsed.format('mm');
        }

        function closeDatePickers(except = null) {
            document.querySelectorAll('.df-datetime-popover.show').forEach(function(popover) {
                if (popover !== except) {
                    popover.classList.remove('show');
                }
            });
        }

        function renderDatePicker(input, popover, state) {
            const monthStart = new Date(state.view.getFullYear(), state.view.getMonth(), 1);
            const gridStart = new Date(monthStart);
            gridStart.setDate(gridStart.getDate() - gridStart.getDay());
            const monthTitle = moment(state.view).format('MMMM YYYY');
            let daysHtml = '';

            for (let i = 0; i < 42; i++) {
                const day = new Date(gridStart);
                day.setDate(gridStart.getDate() + i);
                const isCurrentMonth = day.getMonth() === state.view.getMonth();
                const isSelected = state.selected && moment(day).isSame(state.selected, 'day');
                daysHtml += `<button type="button" class="df-datetime-day ${isCurrentMonth ? '' : 'muted'} ${isSelected ? 'selected' : ''}" data-date="${moment(day).format('YYYY-MM-DD')}">${day.getDate()}</button>`;
            }

            popover.innerHTML = `
                <div class="df-datetime-head">
                    <button type="button" class="df-datetime-nav" data-action="prev"><em class="icon ni ni-chevron-left"></em></button>
                    <div class="df-datetime-title">${monthTitle}</div>
                    <button type="button" class="df-datetime-nav" data-action="next"><em class="icon ni ni-chevron-right"></em></button>
                </div>
                <div class="df-datetime-week"><span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span></div>
                <div class="df-datetime-grid">${daysHtml}</div>
                <div class="df-datetime-time">
                    <input type="number" class="form-control" min="0" max="23" value="${state.hour}" data-time="hour" aria-label="Hour">
                    <input type="number" class="form-control" min="0" max="59" value="${state.minute}" data-time="minute" aria-label="Minute">
                </div>
                <div class="df-datetime-actions">
                    <button type="button" class="btn btn-sm btn-light" data-action="clear">Clear</button>
                    <button type="button" class="btn btn-sm btn-primary" data-action="apply">Apply</button>
                </div>
            `;

            popover.querySelector('[data-action="prev"]').addEventListener('click', function() {
                state.view = new Date(state.view.getFullYear(), state.view.getMonth() - 1, 1);
                renderDatePicker(input, popover, state);
            });

            popover.querySelector('[data-action="next"]').addEventListener('click', function() {
                state.view = new Date(state.view.getFullYear(), state.view.getMonth() + 1, 1);
                renderDatePicker(input, popover, state);
            });

            popover.querySelectorAll('.df-datetime-day').forEach(function(button) {
                button.addEventListener('click', function() {
                    state.selected = moment(this.dataset.date, 'YYYY-MM-DD').toDate();
                    state.view = state.selected;
                    renderDatePicker(input, popover, state);
                });
            });

            popover.querySelector('[data-action="clear"]').addEventListener('click', function() {
                input.value = '';
                popover.classList.remove('show');
                $(input).trigger('change');
            });

            popover.querySelector('[data-action="apply"]').addEventListener('click', function() {
                const hour = popover.querySelector('[data-time="hour"]').value.padStart(2, '0').slice(-2);
                const minute = popover.querySelector('[data-time="minute"]').value.padStart(2, '0').slice(-2);
                const selected = state.selected || new Date();
                state.hour = String(Math.min(Math.max(parseInt(hour || '0', 10), 0), 23)).padStart(2, '0');
                state.minute = String(Math.min(Math.max(parseInt(minute || '0', 10), 0), 59)).padStart(2, '0');
                input.value = `${moment(selected).format('YYYY-MM-DD')} ${state.hour}:${state.minute}`;
                popover.classList.remove('show');
                $(input).trigger('change');
            });
        }

        function datatable1() {
            NioApp.DataTable('#myTable', {
                processing: true,
                serverSide: true,
                searching: true,
                bLengthChange: true,
                ajax: {
                    url: "{{ url('admin/activity_logs/all-data') }}",
                    type: "GET",
                    data: function(d) {
                        d.from_date_time = $("#from_date_time").val();
                        d.to_date_time = $("#to_date_time").val();
                        d.system_user_id = $("#system_user_id").val();
                    }
                },
                ordering: false,
                responsive: !0,
                lengthMenu: [[10, 100, 500, -1], [10, 100, 500, "All"]],
                drawCallback: function() {
                    initActivityLogTooltips();
                },
                columns: [
                    {
                        data: null,
                        render: function(data, type, row) {
                            let causer = row.causer || {};
                            return `<div><strong>${escapeHtml(causer.name || 'System')}</strong><div class="small text-muted">${escapeHtml(causer.email || '')}</div><div class="small text-muted">${escapeHtml(causer.phone_number || '')}</div></div>`;
                        }
                    },
                    { data: "log_name" },
                    { data: "event" },
                    {
                        data: "description",
                        render: function(data) {
                            return data ? escapeHtml(data) : 'NA';
                        }
                    },
                    {
                        data: "properties",
                        render: function(data) {
                            if (!data || Object.keys(data).length === 0) {
                                return "<span class='text-muted'>{}</span>";
                            }
                            let prettyJson = JSON.stringify(data, null, 2);
                            let displayText = prettyJson.length > 80 ? prettyJson.substr(0, 80) + '...' : prettyJson;
                            return `<span data-bs-toggle="tooltip" data-bs-placement="left" title="${escapeHtml(prettyJson)}" style="cursor:help; display:inline-block; max-width:260px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><code>${escapeHtml(displayText)}</code></span>`;
                        }
                    },
                    {
                        data: "created_at",
                        render: function(data) {
                            return moment(data).format('DD-MM-YYYY HH:mm A');
                        }
                    }
                ]
            });
            activityLogTable = $('#myTable').DataTable();
        }
    </script>
@endsection
