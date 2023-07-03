<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteCommentRequest;
use App\Mail\SendingMailAssignJob;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class CommentController extends CoreController
{
    public $data = [];

    public function __construct() {

    }

    public function postComment(Request $request) {
        $job_proccess_id = $this->data['job_proccess_id'] = $request->job_proccess_id;
        $data_comment = [
            'job_proccess_id'   => $request->job_proccess_id,
            'content'           => trim($request->comment_content),
            'created_at'        => date('Y-m-d H:i:s'),
            'created_by'        => Auth::user()->id
        ];
        DB::beginTransaction();
        try {
            if ($request->hasFile('file_attach')) {
                $file = $request->file('file_attach');
                $filename = $file->hashName();

                $check_put_file_to_disk = Storage::disk('comments')->put($filename, file_get_contents($file));

                if ($check_put_file_to_disk) { //Storage::disk('comments')->exists('yJLHrJo1EbiC7mA0kB3b9ef1lhlObrNWgQdqcTLG.png');
                    $data_comment = array_merge($data_comment, ['pathfile' => $filename]);
                }
            }
            DB::table('comments')->insert($data_comment);
            $this->data['data_comments'] = Comment::with('created_by')
                                                    ->where('job_proccess_id', $job_proccess_id)
                                                    ->orderBy('id', 'asc')
                                                    ->get();
            foreach ($this->data['data_comments'] as $key => $comment_item) {
                if ($comment_item->pathfile)
                {
                    $comment_item->pathinfo = pathinfo($comment_item->pathfile);
                    $comment_item->url_path = url('/uploads/comments/'. $comment_item->pathfile);
                }
            }
            $this->data['number_comments'] = CountNumberCommentByJobId($job_proccess_id);
            return $this->responseJSONSuccessWithData($this::IS_COMMIT_DB, "OK", $this->data);
        } catch (\Throwable $th) {
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, $th->getMessage() . ' line: ' . $th->getLine());
        }
    }

    public function getListCommentsData(Request $request)
    {
        $limit = $request->limit != null ? $request->limit : 10; // page_size
        $offset = $request->offset != null ? $request->offset : 0;
        $job_proccess_id = $request->job_proccess_id;

        $data = Comment::with(['children' => function($query) {
                            $query->with('created_by');
                        }, 'created_by'])
                        ->when($job_proccess_id, function ($query) use ($job_proccess_id) {
                            $query->where('job_proccess_id', $job_proccess_id);
                        })
                        ->orderBy('id', 'asc');

        $count = $data->count();
        // $data_service = $data->offset($offset)->limit($limit)->get();
        $data_comments = $data->get();
        foreach ($data_comments as $key => $comment_item) {
            if ($comment_item->pathfile)
            {
                $comment_item->pathinfo = pathinfo($comment_item->pathfile);
                $comment_item->url_path = url('/uploads/comments/'. $comment_item->pathfile);
            }
        }
        $number_comments = CountNumberCommentByJobId($job_proccess_id);
        return response()->json([
            "success"           => true,
            "message"           => "Lấy dữ liệu thành công",
            // 'rows'           => $data_comments,
            'data_comments'     => $data_comments,
            'number_comments'   => $number_comments,
        ], 200);
    }

    public function deleteComment(DeleteCommentRequest $request) {
        $id = $request->id;
        $this->data['parent_id'] = $request->parent_id;
        $check_comment_info = Comment::where('id', $id)->where('created_by', Auth::user()->id)->first();
        if (! $check_comment_info) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Không có quyền xóa bình luận này");
        }
        DB::beginTransaction();
        try {
            
            DB::table('comments')->where('id', $id)->where('created_by', Auth::user()->id)->delete();
            return $this->responseJSONSuccessWithData($this::IS_COMMIT_DB, "Đã xóa bình luận");
        } catch (\Throwable $th) {
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, $th->getMessage());
        }
    }

    public function testMail(Request $request) {
        dd(public_path('uploads/comments'));
        dd(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', "Tiếng việt"));
        dd(strtr(utf8_decode("tiếng việt"), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'));
        dd(strtolower('longLv197@gmail.com'));
        // lower
        // try {
        //     $email  = 'longlv197@gmail.com';

        //     $sending_mail = new SendingMailAssignJob();
        //     Mail::to($email)->send($sending_mail);
        //     return 'ok';
        // } catch (\Throwable $th) {
        //     return $th->getMessage();
        // }
    }
}
