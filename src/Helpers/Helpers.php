<?php

use SecTheater\Jarvis\Activation\ActivationRepository;
use SecTheater\Jarvis\Activation\EloquentActivation;
use SecTheater\Jarvis\Comment\CommentRepository;
use SecTheater\Jarvis\Comment\EloquentComment;
use SecTheater\Jarvis\Exceptions\HelperException;
use SecTheater\Jarvis\Like\EloquentLike;
use SecTheater\Jarvis\Like\LikeRepository;
use SecTheater\Jarvis\Post\EloquentPost;
use SecTheater\Jarvis\Post\PostRepository;
use SecTheater\Jarvis\Reminder\EloquentReminder;
use SecTheater\Jarvis\Reminder\ReminderRepository;
use SecTheater\Jarvis\Reply\EloquentReply;
use SecTheater\Jarvis\Reply\ReplyRepository;
use SecTheater\Jarvis\Role\EloquentRole;
use SecTheater\Jarvis\Role\RoleRepository;
use SecTheater\Jarvis\Tag\EloquentTag;
use SecTheater\Jarvis\Tag\TagRepository;
use SecTheater\Jarvis\User\EloquentUser;
use SecTheater\Jarvis\User\UserRepository;

if (!function_exists('user')) {
    function user($guard = null)
    {
        return auth()->guard($guard)->check() ? auth()->user() : null;
    }
}
if (!function_exists('getUser')) {
    function getUser(array $condition)
    {
        if ((count($condition) === 1) && array_key_exists('id', $condition)) {
            return \UserRepository::find($condition['id']);
        }

        return \UserRepository::findBy($condition);
    }
}

if (!function_exists('model_exists')) {
    function model_exists($name)
    {
        if (File::exists(str_replace('\\', DIRECTORY_SEPARATOR, base_path(lcfirst(config('jarvis.models.namespace.user'))).ucfirst($name).'.php'))) {
            return true;
        }
        if (File::exists(__DIR__.'/../'.str_replace('Eloquent', '', $name).'/'.ucfirst($name).'.php')) {
            return true;
        }

        return false;
    }
}
if (!function_exists('model')) {
    function model(string $name)
    {
        if (File::exists(str_replace('\\', DIRECTORY_SEPARATOR, base_path(lcfirst(config('jarvis.models.namespace.user'))).ucfirst($name).'.php'))) {
            $model = config('jarvis.models.namespace.user').ucfirst($name);

            return new $model();
        } elseif (File::exists(__DIR__.'/../'.str_replace('Eloquent', '', $name).'/'.ucfirst($name).'.php')) {
            $model = '\\SecTheater\\Jarvis\\'.str_replace('Eloquent', '', $name).'\\'.ucfirst($name);

            return new $model();
        }

        throw new HelperException("Model $name Does not exist", 404);
    }
}
if (!function_exists('Jarvis')) {
    function Jarvis()
    {
        $user = new UserRepository(new EloquentUser());
        $activation = new ActivationRepository(new EloquentActivation());
        $role = new RoleRepository(new EloquentRole());
        $post = new PostRepository(new EloquentPost());
        $comment = new CommentRepository(new EloquentComment());
        $reply = new ReplyRepository(new EloquentReply());
        $reminder = new ReminderRepository(new EloquentReminder());
        $like = new LikeRepository(new EloquentLike());
        $tag = new TagRepository(new EloquentTag());

        return new \SecTheater\Jarvis\Jarvis(
            $user,
            $activation,
            $role,
            $post,
            $comment,
            $reply,
            $reminder,
            $like,
            $tag
        );
    }
}
