<?php

namespace App\Http\Livewire;

use App\Models\CookieRecord;
use App\Services\Loader;
use App\Services\Netflix;
use Livewire\Component;
use Livewire\WithPagination;

class Board extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search = "";
    public $active = "all";
    public $code = "";
    public $cookie;

    public function mount()
    {
        //
    }

    public function render()
    {
        return view('livewire.board', [
            'cookies' => CookieRecord::when($this->search, function ($query) {
                $query->where('email', 'like', '%' . $this->search . '%');
            })->when($this->active, function ($query) {
                $active = $this->active;

                if ($active === "all") {
                    return;
                } elseif ($active === "active") {
                    $query->where('is_active', true);
                } elseif ($active === "inactive") {
                    $query->where('is_active', false)->whereNotNull("is_active");
                }
            })->paginate(10)
        ]);
    }

    public function login(CookieRecord $cookie)
    {
        $netflix = new Netflix();

        if ($netflix->login($cookie)) {
            $profiles = $netflix->getProfiles();

            $cookie->update([
                'profiles' => $profiles,
                "is_active" => true,
            ]);

            // $first = $profiles[0];
            // $netflix->switchProfile($first);
            // auth will be in next step after user enter code
            // $response =  $netflix->authTv(request('code'));


            $this->cookie = $cookie;
        } else {
            $cookie->delete();
            $this->cookie = null;
        }
    }

    public function switchProfile($profile)
    {
        $this->netflix->switchProfile($profile);
    }

    public function authTv()
    {
        $code = $this->code;
        $cookie = $this->cookie;

        if (strlen($code) !== 8) {
            session()->flash('tv_auth_error', 'Code must be 8 characters');
            return false;
        }

        $netflix = new Netflix();

        if ($netflix->login($cookie)) {
            $response =  $netflix->authTv($code);

            if ($response) {
                session()->flash('tv_auth_success', 'TV Auth Success');
            } else {
                session()->flash('tv_auth_error', 'TV Auth Error');
            }
        }
    }

    public function delete(CookieRecord $cookie)
    {
        $cookie->delete();
    }

    public function load()
    {
        $loader = new Loader();
        $loader->load(clear: false);
    }
}
