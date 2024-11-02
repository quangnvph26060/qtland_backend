<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\CommentImage;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class ImageController extends Controller
{

    /**
     * Hàm upload ảnh
     * @param Request $request
     * @return array
     * CreatedBy: youngbachhh (12/04/2024)
     */
    function upload(Request $request)
    {
        $appUrl = env('APP_URL');
        if ($request->hasFile('files')) {
            $post_id = $request->post_id;
            $i = 1;

            foreach ($request->file('files') as $file) {
                $timestamp = now()->format('YmdHis');
                $directoryName = 'post-' . $post_id;
                $fileName = $timestamp . "_" . $i . '.' . $file->getClientOriginalExtension();

                // Đường dẫn lưu trữ trong Storage
                $publicPath = 'upload/images/posts/' . $directoryName . '/' . $fileName;

                // Tạo thư mục nếu chưa tồn tại
                Storage::makeDirectory('upload/images/posts/' . $directoryName);

                // Lưu file vào storage (cả local và public storage đều được cấu hình trong 'public' disk)
                $file->storeAs('upload/images/posts/' . $directoryName, $fileName, 'public');

                // Đường dẫn URL cho ảnh đã lưu
                $storageUrl = asset('storage/upload/images/posts/' . $directoryName . '/' . $fileName);

                // Tạo bản ghi trong cơ sở dữ liệu
                PostImage::create([
                    'post_id' => $post_id,
                    'image_path' => $storageUrl
                ]);

                $i++;
            }
            return ['success' => 'Upload thành công'];
        } else {
            return ['error' => 'Không tìm thấy file để upload'];
        }
    }


    function update(Request $request)
    {
        $appUrl = env('APP_URL');
        $post_id = $request->post_id;

        // Xóa hết các ảnh trong thư mục của bài viết
        $directoryName = 'post-' . $post_id;
        $directoryPath = 'public/upload/images/posts/' . $directoryName;
        $post = Post::find($request->post_id);
        $images = $post->postImage;

        $deleted_files = json_decode($request->deleted_files);

        // Xóa ảnh đã chọn
        foreach ($images as $image) {
            $check = false;
            for ($i = 0; $i < count($deleted_files); $i++) {
                if ($image->id == $deleted_files[$i]->id) {
                    $check = true;
                    break;
                }
            }
            if ($check == true) {
                preg_match('/(\d+)/', basename($image->image_path), $matches);
                $i = $matches[0];
                continue;
            }
            $image->delete();
            Storage::delete('public/upload/images/posts/' . $directoryName . '/' . basename($image->image_path));
        }

        // Lưu ảnh mới
        $i = 0;
        foreach ($request->file('files') as $file) {
            $timestamp = now()->format('YmdHis');
            $directoryName = 'post-' . $post_id;
            $fileName = $timestamp . "_" . $i . '.' . $file->getClientOriginalExtension();

            // Đường dẫn lưu trữ trong Storage
            $publicPath = 'upload/images/posts/' . $directoryName . '/' . $fileName;

            // Tạo thư mục nếu chưa tồn tại
            Storage::makeDirectory('upload/images/posts/' . $directoryName);

            // Lưu file vào storage (cả local và public storage đều được cấu hình trong 'public' disk)
            $file->storeAs('upload/images/posts/' . $directoryName, $fileName, 'public');

            // Đường dẫn URL cho ảnh đã lưu
            $storageUrl = asset('storage/upload/images/posts/' . $directoryName . '/' . $fileName);

            // Tạo bản ghi trong cơ sở dữ liệu
            PostImage::create([
                'post_id' => $post_id,
                'image_path' => $storageUrl
            ]);

            $i++;
        }


        return response()->json($post, 200);
    }


    function uploadCommentImg(Request $request)
    {
        if ($request->hasFile('files')) {
            $post_id = $request->post_id;
            $comment_id = $request->comment_id;
            $i = 1;
            foreach ($request->file('files') as $file) {
                $directoryName = 'comment-' . ($comment_id);
                $storagePath = 'public/upload/images/posts/' . 'post-' . $post_id . '/comments/' . $directoryName . '/' . $i . '.' . $file->getClientOriginalExtension();
                $storageUrl = 'storage/upload/images/posts/' . 'post-' . $post_id . '/comments/' . $directoryName . '/' . $i . '.' . $file->getClientOriginalExtension();
                Storage::disk('local')->put($storagePath, file_get_contents($file));
                CommentImage::create([
                    'comment_id' => $comment_id,
                    'post_id' => $post_id,
                    'image_path' => "http://127.0.0.1:8000/" . $storageUrl
                ]);
                $i = $i + 1;
            }
            return ['success' => 'Upload thành công'];
        } else {
            return ['error' => 'Không tìm thấy file để upload'];
        }
    }

    function show($id)
    {
        $files = Redis::get('postImage:' . $id);

        if (!$files) {
            $files = scandir(storage_path('app/public/upload/images/posts/post-' . $id));
            $files = array_diff($files, ['.', '..']);
            Redis::set('postImage:' . $id, json_encode($files));
        } else {
            $files = json_decode($files);
        }

        return response()->json($files);
    }

    function delete($filename)
    {
        unlink(storage_path('app/img/' . $filename));
        return ['result' => 'success'];
    }



    function index()
    {
        // $files = scandir(storage_path('app/public/upload/images/posts/post-6'));
        // $files = array_diff($files, ['.', '..']);
        // return response()->json($files);
    }
}
