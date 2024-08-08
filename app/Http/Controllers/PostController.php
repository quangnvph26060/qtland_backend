<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Post;
use App\Models\CommentImage;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    /**
     * HÃ m láº¥y danh sÃ¡ch toÃ n bá»™ bÃ i viáº¿t
     * @param
     * @return $posts
     * CreatedBy: youngbachhh (31/03/2024)
     */
    public function index(Request $request)
    {
        //
        $posts = Post::with(['user', 'status', 'postImage'])->orderBy('created_at', 'DESC');
        if($request->address){
            $posts->where('address', $request->address);
        }
        $posts = $posts->get();
        return response()->json($posts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * HÃ m lÆ°u bÃ i viáº¿t má»›i
     * @param Request $request
     * @return $users
     * CreatedBy: youngbachhh (04/04/2024)
     */
    public function store(Request $request)
    {


        $post = Post::create(
            [
                'title' => $request->title,
                'description' => $request->description,
                'address' => $request->address,
                'address_detail' => $request->address_detail,
                'classrank' => $request->classrank,
                'area' => $request->area,
                'areausable' => $request->areausable,
                'price' => $request->price,
                'priceservice' => $request->priceservice,
                'priceElectricity' => $request->priceElectricity,
                'pricewater' => $request->pricewater,
                'floors' => $request->floors,
                'rooms' => $request->rooms,
                'bathrooms' => $request->bathrooms,
                'bonus' => $request->bonus,
                'bonusmonthly'=> $request->bonusmonthly,
                'direction' => $request->direction,
                'directionBalcony' => $request->directionBalcony,
                'wayin'=> $request->wayin,
                'font' => $request->font,
                'pccc'=> $request->pccc,
                'elevator' => $request->elevator,
                'stairs' =>$request->stairs,
                'unit' => $request->unit,
                'unit1' => $request->unit1,
                'unit2' => $request->unit2,
                'unit3' => $request->unit3,
                'sold_status' => $request->sold_status,
                'status_id' => $request->status_id,
                'priority_status' => $request->priority_status ?? "",
                'updated_at' => date('Y-m-d H:i:s'),
                'user_id' => $request->user_id
            ]
        );




        return response()->json($post, 201);
    }

    /**
     * HÃ m láº¥y thÃ´ng tin bÃ i viáº¿t theo id
     * @param Request $request
     * @return $users
     * CreatedBy: youngbachhh (31/03/2024)
     */
    public function show($id)
    {
        // $post = Redis::get('post:' . $id);

        // if ($post === null) {
        $post = Post::with([
            'user' => function ($query) {
                $query->select('*');
            },
            'status' => function ($query) {
                $query->select('id', 'name');
            },
            'postImage' => function ($query) {
                $query->select('id', 'post_id', 'image_path');
            },
            'comment' => function ($query) {
                $query->select('id', 'post_id', 'user_id', 'content', 'created_at')->orderBy('created_at', 'desc');
            },
            'comment.user' => function ($query) {
                $query->select('id', 'name');
            },
            'comment.commentImage'
        ])->findOrFail($id);


        //     Redis::set('post:' . $id, json_encode($post));
        //     Redis::expire('post:' . $id, 3600);
        // } else {
        //     $post = json_decode($post);
        // }


        return response()->json($post, 200);
    }

    /**
     * HÃ m láº¥y ra danh bÃ i viáº¿t Ä‘ang chá» duyá»‡t
     * @param
     * @return $posts
     * CreatedBy: youngbachhh (31/03/2024)
     */
    public function pending()
    {
        $posts = Redis::get('posts:pending');

        if ($posts === null) {
            $posts = Post::with(['user' => function ($query) {
                $query->select('id', 'name');
            }, 'status' => function ($query) {
                $query->select('id', 'name');
            }, 'postImage'])->where('status_id', 3)
                ->orderBy('created_at', 'desc')
                ->get();

            Redis::set('posts:pending', json_encode($posts));
            Redis::expire('posts:pending', 3600);
        } else {
            $posts = json_decode($posts);
        }

        return response()->json($posts, 200);
    }

    /**
     * HÃ m láº¥y ra danh bÃ i viáº¿t khÃ´ng pháº£i Ä‘ang chá» duyá»‡t
     * @param
     * @return $posts
     * CreatedBy: youngbachhh (31/03/2024)
     */
    public function notPending()
    {
        $posts = Redis::get('posts:not-pending');

        if ($posts === null) {
            $posts = Post::with(['user' => function ($query) {
                $query->select('id', 'name');
            }, 'status' => function ($query) {
                $query->select('id', 'name');
            }, 'postImage'])
                ->where('status_id', '!=', 3)
                ->orderBy('created_at', 'desc')
                ->get();

            Redis::set('posts:not-pending', json_encode($posts));
            Redis::expire('posts:not-pending', 3600);
        } else {
            $posts = json_decode($posts);
        }

        return response()->json($posts, 200);
    }

    /**
     * HÃ m lá»c bÃ i viáº¿t theo giÃ¡ vÃ  diá»‡n tÃ­ch
     * @param Request $request
     * @return $posts
     * CreatedBy: youngbachhh (23/04/2024)
     */
    public function filter(Request $request)
    {
        $page = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 10);
        $priority = $request->input('priority_status', 'all');
        $searchConditions = $request->input('searchConditions', []);

        // Generate a unique cache key based on request parameters
        $cacheKey = 'posts:' . md5(serialize($request->all()));

        $cachedPosts = Redis::get($cacheKey);

        if ($cachedPosts) {
            return response()->json(json_decode($cachedPosts), 200);
        } else {
            $postsQuery = Post::with(['user:id,name,role_id', 'status:id,name', 'postImage'])
                ->withCount('views')
                ->when(!$request->filled('address'), function ($query) {
                    $query->where('status_id', '!=', 3);
                })
                ->when($request->filled('priority_status') && $priority !== 'all', function ($query) use ($priority) {
                    $query->where('priority_status', $priority);
                })
                ->when(!empty($searchConditions), function ($query) use ($searchConditions) {

                    $query->where(function ($query) use ($searchConditions) {
                        foreach ($searchConditions as $condition) {
                            $column = $condition['column'];
                            $text = $condition['text'];

                            if ($column === 'name') {
                                $query->orWhereHas('user', function ($query) use ($text) {
                                    $query->where('name', 'LIKE', '%' . $text . '%');
                                });
                            } elseif (in_array($column, [
                                "title",
                                "address",
                                "address_detail",
                            ])) {
                                $query->Where($column, 'LIKE', '%' . $text . '%');
                            }
                        }
                    });
                })
                ->orderBy('created_at', 'desc');

            // Address search
            if ($request->filled('address')) {
                $postsQuery = $this->applyAddressFilter($postsQuery, $request->address);
            }


            // Log::info($request->all());
            // Apply filters
            $postsQuery = $this->applyFilters($postsQuery, $request);

            // Apply pagination
            $posts = $postsQuery->paginate($pageSize, ['*'], 'page', $page);
            // Cache the result for 10 minutes
            Redis::setex($cacheKey, 600, $posts->toJson());

            return response()->json($posts, 200);
        }
    }



    private function applyAddressFilter($query, $address)
    {
        $query->where(function ($query) use ($address) {
            $query->where('title', 'LIKE', '%' . $address . '%')
                ->orWhere('address', 'LIKE', '%' . $address . '%')
                ->orWhere('address_detail', 'LIKE', '%' . $address . '%');
        });
        // Log::info("Address: " . $address);

        return $query;
    }

    private function applyFilters($query, Request $request)
    {

        // Log::info($request->classrank);
        $defaultMinArea = 0;
        $defaultMaxArea = 1000;
        $defaultMinPrice = 0;
        $defaultMaxPrice = 60000000000;



        // Price range filter
        if ($request->filled(['min_price', 'max_price'])) {
            $minPrice = $request->input('min_price', $defaultMinPrice);
            $maxPrice = $request->input('max_price', $defaultMaxPrice);
            if ($minPrice != $defaultMinPrice || $maxPrice != $defaultMaxPrice) {
                $query->whereBetween('price', [$minPrice, $maxPrice]);
            }
        }

        // Area range filter
        if ($request->filled(['min_area', 'max_area'])) {
            $minArea = $request->input('min_area', $defaultMinArea);
            $maxArea = $request->input('max_area', $defaultMaxArea);
            if ($minArea != $defaultMinArea || $maxArea != $defaultMaxArea) {
                $query->whereBetween('area', [$minArea, $maxArea]);
            }
        }
        if ($request->filled('classrank')) {
            $query->where('classrank','=' ,$request->classrank);
        }

        // Directions filter
        if ($request->filled('dirs') && is_array($request->dirs)) {
            $query->whereIn('direction', $request->dirs);
        }
        if ($request->filled('sold_status') && is_array($request->sold_status)) {
            $query->where('sold_status', $request->sold_status);
        }

        // if ($request->filled('priority_status') && is_array($request->priority_status)) {
        //     $query->where('priority_status', $request->priority_status);
        // }

        return $query;
    }



    /**
     * HÃ m láº¥y ra danh sÃ¡ch bÃ i viáº¿t theo user_id
     * @param Request $request
     * @return $posts
     * CreatedBy: youngbachhh (31/03/2024)
     */
    public function getPostByUser($id)
    {
        $posts = Post::with([
            'user' => function ($query) {
                $query->select('id', 'name');
            },
            'postImage'
        ])
            ->where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($posts, 200);
    }

    /**
     * HÃ m láº¥y ra tá»•ng sá»‘ bÃ i viáº¿t theo user_id
     * @param $id
     * @return $total
     * CreatedBy: youngbachhh (28/04/2024)
     */
    public function totalPostByUser($id)
    {
        $total = Post::where('user_id', $id)->count();
        return response()->json($total, 200);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * HÃ m cáº­p nháº­t thÃ´ng tin bÃ i viáº¿t theo id
     * @param Request $request, Post $post
     * @return $posts
     * CreatedBy: youngbachhh (31/03/2024)
     */

    public function update($id, Request $request)
    {
        $post = Post::find($id);
        $post->update(
            [
                'title' => $request->title,
                'description' => $request->description,
                'address' => $request->address,
                'address_detail' => $request->address_detail,
                'classrank' => $request->classrank,
                'area' => $request->area,
                'areausable' => $request->areausable,
                'price' => $request->price,
                'priceservice' => $request->priceservice,
                'priceElectricity' => $request->priceElectricity,
                'pricewater' => $request->pricewater,
                'floors' => $request->floors,
                'rooms' => $request->rooms,
                'bathrooms' => $request->bathrooms,
                'bonus' => $request->bonus,
                'bonusmonthly' => $request->bonusmonthly,
                'direction' => $request->direction,
                'directionBalcony' => $request->directionBalcony,
                'wayin'=> $request->wayin,
                'font' => $request->font,
                'pccc'=> $request->pccc,
                'elevator' => $request->elevator,
                'stairs' =>$request->stairs,
                'unit' => $request->unit,
                'unit1' => $request->unit1,
                'unit2' => $request->unit2,
                'unit3' => $request->unit3,
                'sold_status' => $request->sold_status,
                'status_id' => $request->status_id,
                'priority_status' => $request->priority_status,
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );
        // if (Redis::exists('post:' . $id)) {
        //     Redis::del('post:' . $id);
        //     Redis::del('posts:pending');
        //     Redis::del('posts:not-pending');
        // }
        return response()->json($post, 200);
    }

    public function updateStatus($id)
    {
        $post = Post::find($id);
        $post->status_id = 4;
        $post->update(
            [
                'status_id' => $post->status_id,
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );
        if (Redis::exists('post:' . $id)) {
            Redis::hset('post:' . $post->id, [
                'status_id' => $post->status_id,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        Redis::del('posts:pending');
        Redis::del('posts:not-pending');
        return response()->json(['message' => 'Cập nhật thành công'], 200);
    }


    /**
     * HÃ m xÃ³a bÃ i viáº¿t theo id
     * @param Post $post
     * @return message
     * CreatedBy: youngbachhh (31/03/2024)
     */
    public function destroy($id)
    {

        $directoryName = 'post-' . $id;
        $post = Post::find($id);
        $images = $post->postImage;
        $comments = Comment::where('post_id', $id)->get();
        $commentImages = CommentImage::where('post_id', $id)->get();

        foreach ($commentImages as $commentImage) {
            $commentImage->delete();
        }

        foreach ($comments as $comment) {
            $comment->delete();
        }
        foreach ($images as $image) {
            $image->delete();
            Storage::delete('public/upload/images/posts/' . $directoryName . '/' . basename($image->image_path));
        }

        $post->delete();
        return response()->json(['message' => 'Xóa thành công'], 200);
    }
}
