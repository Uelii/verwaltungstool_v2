<!--Layout to show all payments-->

@extends('layouts.master')

@section('title')
    INDEX-Payments
@endsection

@section('content')
    <section class="row data">
        <div class="col-md-12">
            <h2>Overview Payments</h2>
            <hr>
            <!--Review
            <form class="form-horizontal" role="form" method="GET" action="{{ url('/getFilteredPayments')}}">
                <div class="form-group filter_payments">
                    <label for="building_id" class="col-sm-1 control-label">Building</label>
                    <div class="col-sm-4">
                        <select id="building_id" class="form-control" name="building_id" required>
                            <option value=""></option>
                            @foreach($buildings as $building)
                                <option value="{{ $building->id }}"> {{ $building->name }}:
                                    {{ $building->street }} {{$building->street_number}},
                                    {{ $building->zip_code }} {{ $building->city }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="month_year" class="col-sm-1 control-label">Month/Year</label>
                    <div class="col-sm-2">
                        <select id="month" class="form-control" name="month" required>
                            <option value=""></option>
                            @foreach($months as $key => $month)
                                <option value={{ $key }}>{{ $month }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <select id="year" class="form-control" name="year" required>
                            <option value=""></option>
                            @foreach($years as $year)
                                <option value={{ $year }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="renter_id" class="col-sm-1 control-label">Renter</label>
                    <div class="col-sm-4">
                        <select id="renter_id" class="form-control" name="renter_id">
                            <option value=""></option>
                            @foreach($renter as $renter)
                                <option value="{{ $renter->id }}"> {{ $renter->last_name }}, {{ $renter->first_name }}:
                                    {{ $renter->street }} {{ $renter->street_number }},
                                    {{ $renter->zip_code }} {{ $renter->city }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-5">
                        <input type="hidden" name="_method" value="GET">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />

                        <button type="submit" id="btnFilterPayments" class="btn btn-primary pull-right"><i class="fa fa-filter" aria-hidden="true"></i></i> Filter</button>
                    </div>
                </div>
            </form>
            -->

            <div class="table-responsive">
                <table id="payments_data" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>Renter</th>
                        <th>Object</th>
                        <th>Amount</th>
                        <th>Amount paid</th>
                        <th>Payment status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                    </thead>

                    <!--Display data-->
                    <tbody>
                    @foreach($payments as $payment)
                        <tr>
                            <td>{{ $payment->renter->last_name }}, {{ $payment->renter->first_name }}</td>
                            <td>{{ $payment->renter->object->name }}: {{ $payment->renter->object->number_of_rooms }}-room, {{ $payment->renter->object->floor_room_number }}</td>
                            <td id="amountTotal_{{ $payment->id }}">{{ $payment->amount_total }} Fr.</td>
                            <td id="amountPaid_{{ $payment->id }}">{{ $payment->amount_paid }} Fr.</td>
                            <td id="isPaid_{{ $payment->id }}">
                                @if($payment->is_paid == 0)
                                <p class="is_paid_no">NOT PAID</p>
                                <label class="checkbox-inline"><input type="checkbox" class="is_paid_checkbox" data-id="[{{ $payment->id }}, {{ $payment->amount_total }}, {{ $payment->amount_paid }}, {{ $payment->is_paid }}]"><i>Mark as paid</i></label>
                                @else
                                <p class="is_paid_yes">PAID</p>
                                @endif
                            </td>
                            <td>{{ $payment->date }}</td>
                            <td>
                                <a href="{{ route('payments.show', $payment->id) }}" class="btn btn-info"><i class="fa fa-eye" aria-hidden="true"></i> Show</a>
                                <a href="{{ route('payments.edit', $payment->id) }}" class="btn btn-primary"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>

                                <button type="button" id="btnOpenModal" class="btn btn-danger" data-id="{{ $payment->id }}" data-toggle="modal"><i class="fa fa-trash" aria-hidden="true"></i> Delete</button>
                            </td>
                        </tr>

                        <!-- Modal -->
                        <div class="modal fade" id="modalDelete_{{ $payment->id }}" tabIndex="-1">
                            <div class="modal-dialog">

                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">
                                            ×
                                        </button>
                                        <h4 class="modal-title">Please confirm</h4>
                                    </div>
                                    <div class="modal-body">
                                        Do you really want to delete this payment?

                                        <h3>{{ $payment->renter->last_name }}, {{ $payment->renter->first_name }}: {{ $payment->amount_total }} Fr.</h3>
                                        <hr>
                                        <p><b>Object: </b>{{ $payment->renter->object->name }}: {{ $payment->renter->object->number_of_rooms }}-room, {{ $payment->renter->object->floor_room_number }}</p>
                                        <p><b>Payment status: </b>
                                            @if($payment->is_paid == 0)
                                                <p class="is_paid_no"><i class="fa fa-times" aria-hidden="true"></i> NOT PAID</p>
                                            @else
                                            <p class="is_paid_yes"><i class="fa fa-check" aria-hidden="true"></i> PAID</p>
                                            @endif
                                        </p>
                                        <p><b>Creation date: </b>{{ date('Y-m-d', strtotime($payment->created_at)) }}</p>
                                        <div class="modal-footer">
                                            <form class="form-horizontal" role="form" method="POST" action="{{ url('/payments', $payment->id)}}">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />

                                                <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Yes</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <script>

                    </script>
                    @endforeach
                </table>

                <!--JavaScript-->
                <script>
                    addSortTableOptions('payments_data');
                    loadBootstrapModal();
                    changeAmountOnCheckboxClick();

                </script>

                <a href="{{ url('/payment/create')}}" class="btn btn-primary"><i class="fa fa-plus-circle" aria-hidden="true"></i> Add payment??</a>
            </div>
        </div>
    </section>
@endsection