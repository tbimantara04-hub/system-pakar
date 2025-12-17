@extends('layouts.dashboard.main')

@section('content')
    <div class="pb-3">
        <h2>Profile</h2>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-grid">
                <div class="card-header">
                    <div class="card-title">Update Profile Information</div>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="card mb-grid">
                <div class="card-header">
                    <div class="card-title">Update Password</div>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="card mb-grid">
                <div class="card-header">
                    <div class="card-title">Delete Account</div>
                </div>
                <div class="card-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
@endsection
