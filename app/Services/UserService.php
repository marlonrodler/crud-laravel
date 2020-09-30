<?php

namespace App\Services;

use App\Filters\Keyword;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\UserCollection as UserResourceCollection;
use App\Models\User;
use App\Services\Auth\LoginService;
use App\Services\Auth\PasswordService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserService
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $query = QueryBuilder::for(User::class)
            ->allowedFilters([
                AllowedFilter::custom(
                    'keyword',
                    Keyword::searchOn(['name', 'email'])
                )
            ])
            ->defaultSort('name')
            ->allowedSorts(['id', 'name', 'email']);

        return new UserResourceCollection(
            $query->paginate(
                (int) $request->per_page
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:' . User::class . ',email',
        ]);

        $user = User::create($request->all());

        try {
            $passwordService = new PasswordService();
            $passwordService->mail($user);
        } catch (\Throwable $th) {
            throw $th;
        }

        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  User  $user
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(User $user, Request $request)
    {

        $request->validate([
            'email' => [
                'sometimes',
                'required',
                Rule::unique(User::class)->ignore($user->id),
            ],
        ]);

        $user->update($request->all());

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {

        (new LoginService())->revokeAllTokens($user);
        $user->delete();

        return response()->json();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User $user
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function status(User $user, Request $request)
    {

        $request->validate([
            'status' => [
                'required',
                Rule::in([
                    User::STATUS_PENDING,
                    User::STATUS_ACTIVE,
                    User::STATUS_BLOCKED
                ]),
            ],
        ]);

        $user->status = $request->get('status');

        $user->save();

        if ($user->status === User::STATUS_BLOCKED) {
            (new LoginService())->revokeAllTokens($user);
        }

        return response()->json();
    }
}
