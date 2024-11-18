<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

use App\Models\RfpBundle;
use App\Models\User;


class upload extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
    
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
       
        $rfpBundles = RfpBundle::all();
        $ListBundles = array();

        //$AgentId = Auth::user()->id;

        foreach ($rfpBundles as $key => $User) {
              $ListBundle = array();
              $ListBundle['id'] = $User->bundle_id;
              $ListBundle['bundle'] = $User->bundle;
              $ListBundle['type'] = $User->type_bundle;
              $ListBundles[] = $ListBundle;
        }


        $data = array(
            'ListBundles' => $ListBundles,
        );


        //return view('auth.register')->with($data);

       return view('components.upload')->with($data);
    }
}
