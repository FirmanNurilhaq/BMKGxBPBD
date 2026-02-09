<?php

namespace App\Models;

use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $table = 'comments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'content', 'created_at'];
    protected $useTimestamps = false;

    /**
     * Get today's comments with user info
     */
    public function getTodayComments()
    {
        $today = date('Y-m-d');
        
        return $this->select('comments.*, users.nama, users.username')
                    ->join('users', 'users.id = comments.user_id')
                    ->where('DATE(comments.created_at)', $today)
                    ->orderBy('comments.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Delete comments older than today (for cleanup if needed)
     */
    public function deleteOldComments()
    {
        $today = date('Y-m-d');
        return $this->where('DATE(created_at) <', $today)->delete();
    }

    /**
     * Get all comments with user info for admin monitoring
     */
    public function getAllComments($date = null)
    {
        $builder = $this->select('comments.*, users.nama, users.username, users.email')
                        ->join('users', 'users.id = comments.user_id')
                        ->orderBy('comments.created_at', 'DESC');

        if ($date) {
            $builder->where('DATE(comments.created_at)', $date);
        }

        return $builder->findAll();
    }
}
