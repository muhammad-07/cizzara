@extends('layouts.app-new')

@section('content')


<div class="row">
    <div class="col-md-6">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Contestants /</span><?php echo date('Y') ?></h4>
    </div>
    <div class="col-md-6">
        <form action="{{ route('admin.auditions.top') }}" method="GET">
            <div class="input-group">
                <select name="audition" style="min-width: 110px;" class="form-select form-select-lg">
                    @foreach($plans as $p)
                    <option value="{{$p->name}}" @if($p->name==request()->audition) selected @endif >{{$p->name}}</option>
                    @endforeach
                </select>
                <select name="status" style="min-width: 110px;" class="form-select">
                    <option value="">All</option>
                    @foreach(config('app.audition_status') as $key => $value)
                    <option value="{{$value}}" @if($value==request()->status) selected @endif >{{$value}}</option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-primary waves-effect" type="button">Filter</button>
            </div>
        </form>
    </div>

</div>
<hr class="mt-0" />

<div class="card">
    <h5 class="card-header">Top contestants</h5>

    <div class="p-3">
        <div class="row">
            <div class="col-md-5">
                @include('partials.export-btns')
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <button id="btn-move" class="btn btn-primary waves-effect" type="button">Move selected to</button>
                    <select id="status-dropdown-all" class="form-select status-dropdown-all">
                        @foreach(config('app.audition_status') as $key => $value)
                        <option value="{{$value}}">{{$value}}</option>
                        @endforeach
                    </select>

                </div>
            </div>
            <div class="col-md-4">
                <form action="{{ route('admin.users.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control" id="contestant" name="contestant" placeholder="Search by Contestant Name" aria-label="Search by Contestant Name" aria-describedby="button-addon2">
                        <button type="submit" name="submit" value="submit" class="btn btn-primary waves-effect" id="button-addon2">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead class="table-light">
                <tr>
                    <td><input class="form-check-input" type="checkbox" name="selectAll" id="selectAll" value="selectAll"></td>
                    <th>Contestant</th>
                    <th>Videos</th>
                    <th>Action</th>
                    @foreach($gurus ?? [] as $guru)
                    <th>{{$guru->name}}</th>
                    @endforeach
                    <th>Avg Rating</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Birthdate</th>
                    <th>Education</th>
                    <th>Occupation</th>
                    <th>City</th>
                    <th>State</th>
                    <th>Zip</th>
                    <th>Address</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">

                @forelse ($topUsers as $audition)
                @php

                $planName = App\Models\Plan::where('id',$audition->plan_id)->first()->name;
                $rowno = strtoupper( str_split($planName)[0] .str_split($planName)[2] . (strlen($audition->id) < 2 ? '0' : '' ).$audition->id);
                    @endphp
                    <tr>
                        <td><input data-plan="{{ $audition->plan_id }}" data-user="{{ $audition->user->id }}" class="form-check-input" type="checkbox" name="selectedRecords[]" value="{{ $audition->id }}">&nbsp; {{ $rowno }}</td>
                        <td><a href="{{ route('admin.users.show', $audition->user) }}">{{ $audition->user->details->first_name . ' ' . $audition->user->details->last_name }}</a></td>



                        <td>
                            @php
                            $videoRatings = [];

                            foreach ($audition->user->videos as $video) {

                            echo '<a href="'. route('admin.videos.show', $video) .'">' .$video->original_name.' </a>
                            <span class="badge rounded-pill bg-label-secondary">'.$video->style.'</span>
                            <br />';

                            $averageRating = $video->ratings->avg('rating');
                            $videoRatings[] = $averageRating;
                            }


                            if (count($videoRatings) > 1) {
                            $userAverageRating = array_sum($videoRatings) / count($videoRatings);
                            } else {
                            $userAverageRating = $videoRatings[0] ?? 0;
                            }


                            @endphp
                        </td>

                        <td>
                            <select style="min-width: 110px;" class="form-select status-dropdown" data-plan="{{ $audition->plan_id }}" data-user="{{ $audition->user->id }}">
                                @foreach(config('app.audition_status') as $key => $value)
                                <option value="{{$value}}" @if($value==$audition->status) selected @endif >{{$value}}</option>
                                @endforeach
                            </select>
                        </td>
                        @foreach($gurus ?? [] as $guru)
                        <td>



                            @foreach ($audition->user->videos as $video)
                            @foreach($video->ratings as $guru_rating)
                            @if($guru_rating->guru_id == $guru->id)
                            {{$guru_rating->rating}}
                            @endif
                            @endforeach

<br/>
                            @endforeach
                        </td>
                        @endforeach
                        <td>{{ $userAverageRating }}</td>
                        <td>{{ $audition->user->email }}</td>

                        <td>{{ $audition->user->details->phone }}</td>
                        <td>{{ $audition->user->details->date_of_birth }}</td>
                        <td>{{ $audition->user->details->education }}</td>
                        <td>{{ $audition->user->details->occupation }}</td>
                        <td>{{ $audition->user->details->city }}</td>
                        <td>{{ $audition->user->details->state }}</td>
                        <td>{{ $audition->user->details->pin_code }}</td>
                        <td>{{ $audition->user->details->address }}</td>


                    </tr>
                    @empty
                    <tr>
                        <td colspan="4">No records found.</td>
                    </tr>
                    @endforelse
            </tbody>
        </table>
    </div>
    <div class="justify-content-center">
        <div class="col-md-6 mx-auto">
            <hr />
            {{-- $paginatedTopUsers->appends(request()->input())->links() --}}
        </div>
    </div>

</div>

@endsection

@section('bottom')
<script src="{{ asset('assets/js/export.js') }}"></script>
<script>
    $(document).ready(function() {
        $('.status-dropdown').change(function() {

            var userIds = [],
                auditionIds = [];
            userIds.push($(this).data('user'));
            auditionIds.push($(this).data('plan'));

            var newStatus = $(this).val();


            moveSelectedTo(newStatus, userIds, auditionIds)


        });

        $('#btn-move').click(async function(e) {
            e.preventDefault();
            var userIds = [],
                auditionIds = [];
            $('input[name="selectedRecords[]"]:checked').each(function() {
                userIds.push($(this).data('user'));
                auditionIds.push($(this).data('plan'));
            });
            if (userIds.length == 0) {
                Toast.fire({
                    icon: 'error',
                    title: 'Select at least one checkbox',
                });
                return;
            }
            var newStatus = $("#status-dropdown-all").val();
            try {
                await moveSelectedTo(newStatus, userIds, auditionIds);
                window.location.replace(window.location.href);
            } catch (error) {
                console.error(error);
                Toast.fire({
                    icon: 'error',
                    title: 'Could not update status',
                });
            }
        });

        function moveSelectedTo(newStatus, userIds, auditionIds) {
            console.log(newStatus, auditionIds, userIds);
            return new Promise(function(resolve, reject) {
                $.ajax({
                    type: 'POST',
                    url: "{{ route('admin.auditions.updateStatus') }}",
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: newStatus,
                        audition: auditionIds,
                        user: userIds,
                    },
                    success: function(response) {
                        Toast.fire({
                            icon: 'success',
                            title: 'Status updated successfully',
                        });
                        resolve(response);
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        reject(error);
                    }
                });
            });
        }
    });
</script>
@endsection
