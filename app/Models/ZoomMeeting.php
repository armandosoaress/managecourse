<?php

namespace App\Models;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZoomMeeting extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'meeting_id', 'course_id', 'student_id','start_date','duration','start_url','password','join_url','status','created_by',
    ];

    protected $appends  = array(
        'student_name',
        'course_name',
    );

    private static $student = [];

    public function getStudentNameAttribute($value)
    {
        $student = Student::select('id', 'name')->where('id', $this->student_id)->first();

        return $student ? $student->name : '';
    }

    public function getCourseNameAttribute($value)
    {
        $course = '';
        if(is_null($this->course_id))
        {
            $course = Course::select('id', 'title')->where('id', $this->course_id)->first();
        }
        return $course ? $course->title : '';
    }

    public function getCourseInfo()
    {
        return $this->hasOne('App\Models\Course', 'id', 'course_id');
    }

    public function getStudentInfo()
    {
        return $this->hasOne('App\Models\Student', 'id', 'student_id');
    }

    public function checkDateTime(){
        $m = $this;
        if (\Carbon\Carbon::parse($m->start_date)->addMinutes($m->duration)->gt(\Carbon\Carbon::now())) {
            return 1;
        }else{
            return 0;
        }
    }

    public function students($students)
    {
        if(!array_key_exists($students,self::$student)){
            $studentArr = explode(',', $students);
            $student = Student::whereIn('id',$studentArr)->get();
            self::$student[$students] = $student;
            $data = self::$student[$students];
        }
        else{
            $data = self::$student[$students];
        }
        return $data;
    }
}
