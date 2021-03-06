<?php

namespace SecTheater\Jarvis\User;

use Cache;
use SecTheater\Jarvis\Repositories\Repository;

class UserRepository extends Repository implements UserInterface
{
    protected $model;

    public function __construct(EloquentUser $model)
    {
        $this->model = $model;
    }

    public function getUsersHave($relation, $operator = '=', $condition = null)
    {
        if (is_array($condition) || is_array($operator)) {
            list($condition) = [$condition ?? $operator];

            return $this->getUsersWhereHave($relation, $condition);
        }
        if (func_num_args() === 2) {
            list($relation, $condition) = func_get_args();

            return $this->model->has($relation, $operator, $condition)->get();
        } elseif (func_num_args() === 3) {
            return $this->model->has($relation, $operator, $condition)->get();
        }

        return $this->model->has($relation)->get();
    }

    public function getUsersDoesntHave($relation, array $condition = null)
    {
        if ($condition) {
            return $this->model->whereDoesntHave($relation, function ($query) use ($condition) {
                return $query->where($condition);
            })->get();
        }

        return $this->model->doesntHave($relation)->get();
    }

    public function getUsersWhereHave($relation, array $condition)
    {
        return $this->model->whereHas($relation, function ($query) use ($condition) {
            $query->where($condition);
        })->get();
    }

    public function isOnline(int $id)
    {
        return Cache::has('user-is-online-'.$id);
    }

    public function PeopleCommentedOnAPost(\App\Post $post)
    {
        if (config('jarvis.comments.approve')) {
            return $post->comments()->where(['approved' => true, ['user_id', '!=', $post->user->id]])->distinct()->get()->unique('user_id');
        }

        return $post->comments()->where('user_id', '!=', $post->user->id)->distinct()->get()->unique('user_id');
    }

    public function PeopleRepliedOnAPost(\App\Post $post)
    {
        if (config('jarvis.replies.approve')) {
            return $post->replies()->where(['approved' => true, ['user_id', '!=', $post->user->id]])->distinct()->get()->unique('user_id');
        }

        return $post->replies()->where('user_id', '!=', $post->user->id)->distinct()->get()->unique('user_id');
    }
}
