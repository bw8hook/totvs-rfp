<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class titleComponent extends Component
{
    public $textButton;
    public $urlButton;
    public $showButton;
    public $componentType;

    public $titleDescription;
    

    /**
     * Create a new component instance.
     */
    public function __construct($componentType = "list", $titleDescription = "", $showButton = false, $textButton = '', $urlButton = "")
    {    
        $this->titleDescription = $titleDescription; 
        $this->componentType = $componentType;
        $this->showButton = $showButton;
        $this->textButton = $textButton;
        $this->urlButton = $urlButton;        
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        //dd($this->ComponentType);

        return view('components.title-component');
    }
}
