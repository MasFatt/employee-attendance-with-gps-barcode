<?php

namespace App\Http\Livewire\Api;

use Illuminate\Support\Facades\Auth;
use Laravel\Jetstream\InteractsWithBanner;
use Livewire\Component;

class ApiTokenManager extends Component
{
    use InteractsWithBanner;

    /**
     * The create API token form state.
     *
     * @var array
     */
    public $createApiTokenForm = [
        'name' => '',
        'permissions' => [],
    ];

    /**
     * The update API token form state.
     *
     * @var array
     */
    public $updateApiTokenForm = [
        'permissions' => [],
    ];

    /**
     * Indicates if the application is currently managing API token permissions.
     *
     * @var bool
     */
    public $managingApiTokenPermissions = false;

    /**
     * The ID of the API token being managed.
     *
     * @var int|null
     */
    public $managingPermissionsFor;

    /**
     * Indicates if the application is currently displaying the token value.
     *
     * @var bool
     */
    public $displayingToken = false;

    /**
     * The plain text token value.
     *
     * @var string|null
     */
    public $plainTextToken;

    /**
     * Indicates if the application is currently confirming if an API token should be deleted.
     *
     * @var bool
     */
    public $confirmingApiTokenDeletion = false;

    /**
     * The ID of the API token being deleted.
     *
     * @var int|null
     */
    public $apiTokenIdBeingDeleted;

    /**
     * Create a new API token.
     *
     * @return void
     */
    public function createApiToken()
    {
        $this->resetErrorBag();

        $this->validate([
            'createApiTokenForm.name' => ['required', 'string', 'max:255'],
        ]);

        $token = $this->user->createToken(
            $this->createApiTokenForm['name'],
            $this->createApiTokenForm['permissions']
        );

        $this->plainTextToken = explode('|', $token->plainTextToken, 2)[1];

        $this->displayingToken = true;

        $this->createApiTokenForm = [
            'name' => '',
            'permissions' => [],
        ];

        $this->emit('created');
    }

    /**
     * Manage the API token permissions.
     *
     * @param  int  $tokenId
     * @return void
     */
    public function manageApiTokenPermissions($tokenId)
    {
        $this->managingPermissionsFor = $tokenId;
        $this->managingApiTokenPermissions = true;

        $token = $this->user->tokens()->where('id', $tokenId)->first();

        $this->updateApiTokenForm['permissions'] = $token->abilities;
    }

    /**
     * Update the API token's permissions.
     *
     * @return void
     */
    public function updateApiToken()
    {
        $token = $this->user->tokens()->where('id', $this->managingPermissionsFor)->first();

        $token->forceFill([
            'abilities' => $this->updateApiTokenForm['permissions'],
        ])->save();

        $this->managingApiTokenPermissions = false;
    }

    /**
     * Confirm that the API token should be deleted.
     *
     * @param  int  $tokenId
     * @return void
     */
    public function confirmApiTokenDeletion($tokenId)
    {
        $this->apiTokenIdBeingDeleted = $tokenId;
        $this->confirmingApiTokenDeletion = true;
    }

    /**
     * Delete the API token.
     *
     * @return void
     */
    public function deleteApiToken()
    {
        $this->user->tokens()->where('id', $this->apiTokenIdBeingDeleted)->delete();

        $this->confirmingApiTokenDeletion = false;
    }

    /**
     * Get the current user of the application.
     *
     * @return mixed
     */
    public function getUserProperty()
    {
        return Auth::user();
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('api.api-token-manager');
    }
}
