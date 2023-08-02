<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Issue extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'issue_id';

    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';

    const RANK_PARENT = 'parent';
    const RANK_CHILD = 'child';
    const RANK_GRANDCHILD = 'grandchild';

    const COMPLETE_REASON_1 = '対応済み';
    const COMPLETE_REASON_2 = '対応しない';
    const COMPLETE_REASON_3 = '無効';
    const COMPLETE_REASON_4 = '重複';
    const COMPLETE_REASON_5 = '再現しない';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'start_date' => 'datetime:Y-m-d',
        'end_date' => 'datetime:Y-m-d',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'issue_cd',
        'issue_title',
        'issue_desc',
        'issue_priority',
        'version_id',
        'estimated_time',
        'completion_time',
        'complete_reason',
        'assignee_id',
        'milestone_id',
        'start_date',
        'end_date',
        'status_id',
        'kind_id',
        'issue_rank',
        'parent_issue_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kind()
    {
        return $this->belongsTo(Kind::class, 'kind_id', 'kind_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id', 'status_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function issue_categories()
    {
        return $this->hasMany(IssueCategory::class, 'issue_id', 'issue_id');
    }
}
