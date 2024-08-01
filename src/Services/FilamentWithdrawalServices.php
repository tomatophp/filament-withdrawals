<?php

namespace TomatoPHP\FilamentWithdrawals\Services;

use TomatoPHP\FilamentWithdrawals\Services\Contracts\Form;

class FilamentWithdrawalServices
{
    public array $forms = [];

    public function register(Form $form){
        $this->forms[] = $form;
    }

    public function getForms(): array
    {
        return $this->forms;
    }

    public function build(): void
    {
        foreach ($this->forms as $form){
            $checkIfFormExists = \TomatoPHP\FilamentWithdrawals\Models\WithdrawalMethod::where('id', $form->id)->first();
            if(!$checkIfFormExists){
                $newForm = \TomatoPHP\FilamentWithdrawals\Models\WithdrawalMethod::create($form->toArray());
                $newForm->fields()->createMany($form->inputs);
            }
        }
    }
}
