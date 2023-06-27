<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Netflix</h1>
        </div>
    </div>

    <div class="row">
        <button class="btn btn-success" wire:click="load">
            Load
        </button>
    </div>

    @if($cookie)
    <div class="alert alert-success">
        <h3>{{ $cookie->email }}</h3>
        <div class="row">
            @foreach(json_decode($cookie->profiles , true) as $profile)
            @php
                $background = isset($profile['background_url']) ? $profile['background_url'] : null;
                $name = isset($profile['name']) ? $profile['name'] : null;
            @endphp
            <div class="col-md-2">
                <img src="{{ $background  }}" alt="" class="img-fluid">
                <p>{{ $name }}</p>
            </div>
            @endforeach
        </div>
        <textarea class="form-control" rows="10" id="cookies">{{ $cookie->content }}</textarea>
        <button class="btn btn-info mt-2" onclick="copyToClipboard()">
            Copy to clipboard
        </button>
        <hr>
        <div class="row">
            <div class="col-md-10">
                <input wire:model="code" class="form-control" type="text" placeholder="Pin" min="8" max="8">
            </div>
            <div class="col-md-2">
                <button class="btn btn-info" wire:click="authTv">Auth TV</button>
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

    <div class="row">
        <div class="col-md-12">
            <input type="text" class="form-control" placeholder="Search by file name" wire:model="search">
        </div>

        {{-- <div class="col-md-6">
            <select class="form-control" wire:model="active">
                <option value="all">All</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div> --}}

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Cookie</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cookies as $cookie)
                <tr
                    class="text-center @if($cookie->is_active) bg-success @endif  @if(!is_null($cookie->is_active) && $cookie->is_active == false ) bg-danger @endif">
                    <td>{{ $cookie->email }}</td>
                    <td>
                        <a href="#" class="btn btn-success" wire:click="login({{ $cookie->id }})">
                            Login
                        </a>
                        @if(!is_null($cookie->is_active) && $cookie->is_active == false )
                        <a href="#" class="btn btn-danger" wire:click="delete({{ $cookie->id }})">
                            Delete
                        </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $cookies->links() }}

</div>
