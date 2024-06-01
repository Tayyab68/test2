@extends('include.app')
@section('header')
    <script src="{{ asset('asset/script/setting.js') }}"></script>
@endsection

@section('content')
    <div class="row same-height-card">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="page-title w-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="mb-0 fw-semibold">{{ __('settings') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body px-4">
                    <form id="settingsForm" method="post" enctype="multipart/form-data" class="form-border" autocomplete="off">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="currency" class="form-label">{{ __('appNameTitle') }}</label>
                                    <input value="{{ $setting->app_name }}" type="text" name="app_name" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 float-end">
                            <button type="submit" class="btn theme-btn text-white saveButton2">{{ __('save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-8 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="page-title w-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="mb-0 fw-normal">{{ __('changePassword') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body px-4">
                    <form id="changePasswordForm" method="POST">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 position-relative">
                                <div class="form-group">
                                    <label for="appName" class="form-label">{{ __('oldPassword') }}</label>
                                    <input type="password" class="form-control" name="user_password" id="userPassword" required="">
                                    <div class="password-icon">
                                        <i data-feather="eye"></i>
                                        <i data-feather="eye-off"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 position-relative">
                                <div class="form-group">
                                    <label for="appName" class="form-label">{{ __('newPassword') }}</label>
                                    <input type="password" class="form-control" name="new_password" id="newPassword" required="">
                                    <div class="password-icon">
                                        <i data-feather="eye" class="eye1"></i>
                                        <i data-feather="eye-off" class="eye-off1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer p-0">
                            <button type="button" class="btn"></button>
                            <button type="submit" class="btn theme-btn text-white saveButton3">{{ __('changePassword') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
