<div class="container">
    <div class="row">
        <div class="col-lg-6">
            <div class="row">
                <div class="col-md-12">
                    <h1>Netflix</h1>
                    <button class="btn btn-info" wire:click="load">Load cookies</button>
                </div>
            </div>

            @if($cookie)
            <div class="alert alert-success">
                <div class="row">
                    @if(is_array($cookie->profiles) && count($cookie->profiles))
                    @foreach($cookie->profiles as $profile)
                    <div class="col-md-2">
                        <img src="{{ $profile->background_url }}" alt="" class="img-fluid">
                        <p>{{ $profile->name }}</p>
                    </div>
                    @endforeach
                    @endif
                </div>

                <h3>{{ $cookie->email }}</h3>
                <textarea class="form-control" rows="10" id="cookies">{{ $cookie->content }}</textarea>
                <button class="btn btn-info mt-2" onclick="copyToClipboard()">
                    Copy to clipboard
                </button>
                <hr>
                <div class="row">
                    <div class="col-md-4">
                        <input wire:model="code" class="form-control" type="text" placeholder="Pin" min="8" max="8">
                    </div>
                    <div class="col-md-8">
                        <button class="btn btn-warning" wire:click="authTv">Auth TV</button>
                    </div>
                </div>
            </div>

            @if(session("tv_auth_success"))
            <div class="alert alert-success">
                {{ session("tv_auth_success") }}
            </div>
            @endif

            @if(session("tv_auth_error"))
            <div class="alert alert-danger">
                {{ session("tv_auth_error") }}
            </div>
            @endif

            @endif
        </div>

        <div class="col-lg-6">
            <div class="col-md-12">
                <input type="text" class="form-control" placeholder="Search by email" wire:model="search">
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Cookie</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cookies as $cookie)
                    <tr style="cursor: pointer;" wire:click="show({{ $cookie->id }})"
                        class="text-center @if($cookie->is_active) bg-success @endif  @if(!is_null($cookie->is_active) && $cookie->is_active == false ) bg-danger @endif">
                        <td>{{ $cookie->email }}</td>
                        <td>
                            <a href="#" class="btn btn-success" wire:click="login({{ $cookie->id }})">
                                Login
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $cookies->links() }}

        </div>
    </div>
</div>
