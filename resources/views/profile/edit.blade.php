@extends('layouts.app')
@section('title', 'My Profile')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <h2 class="heading-font mb-4">My Profile</h2>

                {{-- Success message --}}
                @if (session('message'))
                    <div class="alert alert-islamic-success mb-4">
                        {{ session('message') }}
                    </div>
                @endif

                <div class="card-islamic p-4">

                    {{-- Name and Email --}}
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', $user->name) }}">
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control"
                                value="{{ old('email', $user->email) }}">
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Current Plan</label>
                            <div>
                                <span class="badge badge-{{ $user->plan }} px-3 py-2">
                                    {{ strtoupper($user->plan) }}
                                </span>
                            </div>
                        </div>

                        <button type="submit" class="btn-emerald btn">
                            Save Changes
                        </button>
                    </form>

                    <hr style="border-color:var(--border)" class="my-4">

                    {{-- Preferences --}}
                    <form method="POST" action="{{ route('profile.preferences') }}">
                        @csrf

                        <h5 class="heading-font mb-3">Reading Preferences</h5>

                        <div class="mb-3">
                            <label class="form-label">Preferred Language</label>
                            <select name="preferred_language" class="form-select">
                                <option value="en" {{ $user->preferred_language === 'en' ? 'selected' : '' }}>English
                                </option>
                                <option value="ur" {{ $user->preferred_language === 'ur' ? 'selected' : '' }}>Urdu
                                </option>
                                <option value="ar" {{ $user->preferred_language === 'ar' ? 'selected' : '' }}>Arabic
                                </option>
                            </select>
                        </div>

                        <button type="submit" class="btn-emerald btn">
                            Save Preferences
                        </button>
                    </form>

                    <hr style="border-color:var(--border)" class="my-4">

                    {{-- Change Password --}}
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('PUT')

                        <h5 class="heading-font mb-3">Change Password</h5>

                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>

                        <button type="submit" class="btn-emerald btn">
                            Update Password
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
