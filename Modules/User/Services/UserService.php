<?php

namespace Modules\User\Services;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Core\Services\BaseService;
use Modules\User\Repositories\Interfaces\RoleRepositoryInterface;
use Modules\User\Repositories\Interfaces\UserRepositoryInterface;

class UserService extends BaseService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected RoleRepositoryInterface $roleRepository,
    ) {
    }

    /**
     * Index
     *
     * @param array $filterable
     * @param array $relationships
     *
     * @return mixed
     */
    public function index(array $filterable, array $relationships = []): mixed
    {

        $relationships = array_merge($relationships, [
            "formSubmissions",
        ]);

        return $this->userRepository->fetchAll(
            filterable: $filterable,
            with: $relationships
        );
    }

    /**
     * Get user's profile
     *
     * @return mixed
     */
    public function show(string $id): mixed
    {
        $user = Auth::guard('api')->user();

        return $this->userRepository->fetch(
            id: $user->id,
        );
    }

    /**
     * Update user's profile
     *
     * @param array $data
     *
     * @return void
     */
    public function updatePassword(array $data): void
    {

        $user = Auth::user();
        if (
            isset($data["old_password"])
            && !Hash::check($data["old_password"], $user->password)
        ) {
            throw new \Exception(
                message: $this->lang(
                    key: "auth.old-password",
                    module: "user::app"
                ),
                code: Response::HTTP_BAD_REQUEST
            );
        }

        $this->userRepository->update($data, $user->id);
    }

    /**
     * Store
     *
     * @param array $data
     *
     * @return void
     */
    public function store(array $data): void
    {
        $role = $this->roleRepository->fetch($data['role_id']);

        $data["password"] = Hash::make($data["password"]);

        $user = $this->userRepository->store($data);

        $user->assignRole([$role->name]);
    }

    /**
     * Update
     *
     * @param array $data
     * @param string $id
     *
     * @return void
     */
    public function update(array $data, string $id): void
    {
        $role = $this->roleRepository->fetch($data['role_id']);

        $user = $this->userRepository->update($data, $id);
        $user->removeRole($user->getRoleNames());
        $user->assignRole([$role->name]);
    }

    /**
     * Delete
     *
     * @param string $id
     *
     * @return object
     */
    public function destroy(string $id): object
    {
        return $this->userRepository->delete($id);
    }
}