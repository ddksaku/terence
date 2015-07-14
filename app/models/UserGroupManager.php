<?php

namespace Models;

class UserGroupManager
{
    public static function getCMSGroups($excluding = array())
    {
        $query = \Models\UserGroup::where('group_level', '>', 1)->orderBy('group_level', 'desc');

        return $query->get();
    }
}