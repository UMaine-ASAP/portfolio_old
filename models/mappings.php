<?php


class ConnectionProjectMap extends Model
{
	public static $_table = "REPO_Collection_project_map";
	public static $_id_column = "collect_id";
}


class ProjectMediaMap extends Model
{
	public static $_table = 'REPO_Project_media_map';
	public static $_id_column = 'proj_id';
}

class PortfolioProjectMap extends Model
{
	public static $_table = 'REPO_Portfolio_project_map';
	public static $_id_column = 'port_id';
}

class AssignmentAccessMap extends Model
{
	public static $_table = 'REPO_Assignment_access_map';
	public static $_id_column = 'id';
}

class PortfolioAccessMap extends Model
{
	public static $_table = 'REPO_Portfolio_access_map';
	public static $_id_column = 'id';
}

class MediaAccessMap extends Model
{
	public static $_table = 'REPO_Media_access_map';
	public static $_id_column = 'id';
}

class ProjectAccessMap extends Model
{
	public static $_table = 'REPO_Project_access_map';
	public static $_id_column = 'id';
}

class GroupUserRoleMap extends Model
{
	public static $_table = 'AUTH_Group_user_role_map';
	public static $_id_column = 'group_id';
}

class GroupUserMap extends Model
{
	public static $_table = 'AUTH_Group_user_map';
	public static $_id_column = 'user_id';
}

?>
