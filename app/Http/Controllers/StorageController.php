<?php

namespace App\Http\Controllers;

use App\Http\Middleware\CheckJwtToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class StorageController
 * @package App\Http\Controllers
 */
class StorageController extends Controller
{
    /**
     * StorageController constructor
     */
    public function __construct()
    {
        $this->middleware(CheckJwtToken::class);
    }

    /**
     * @param string $fid
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function get($fid, Request $request)
    {
        $fileName = $request->get('filename');
        $directory = $this->getFileDirectory($request->domain, $fid);
        $fileHash = $this->getFileHash($request->domain, $fid, $fileName);

        $file = sprintf('%s/%s', $directory, $fileHash);
        if (!file_exists($file)) {
            abort(404);
        }

        return response()->download($file, $fileName);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function upload(Request $request)
    {
        $file = $request->file('userfile');
        if (!($file instanceof UploadedFile)) {
            return $this->uploadError('file_not_given');
        }

        $fileName = $file->getClientOriginalName();
        $fileName = $this->cutFileName($fileName);
        $fileHash = md5(sprintf('%s--%s--%s', $request->domain, time(), $fileName));

        $uploadPath = $this->getFileDirectory($request->domain, $fileHash);
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $file->move($uploadPath, $this->getFileHash($request->domain, $fileHash, $fileName));

        $url = sprintf(
            '%s/storage/%s?filename=%s',
            $request->getSchemeAndHttpHost(), $fileHash, urlencode($fileName)
        );

        return JsonResponse::create([
            'state' => 1,
            'success' => true,
            'file' => $request->query->has('trumbowyg') ? $url : $fileHash,
            'url' => $url,
            'name' => $fileName,
        ]);
    }

    /**
     * @param string $fid
     * @param Request $request
     * @return JsonResponse
     */
    public function remove($fid, Request $request)
    {
        $fileName = $request->get('filename');
        $directory = $this->getFileDirectory($request->domain, $fid);
        $fileHash = $this->getFileHash($request->domain, $fid, $fileName);

        $file = sprintf('%s/%s', $directory, $fileHash);
        if (!file_exists($file)) {
            return JsonResponse::create(null, 404);
        }
        unlink($file);
        return JsonResponse::create([
            'state' => 1,
            'success' => true,
        ]);
    }

    /**
     * @param string $code
     * @return JsonResponse
     */
    protected function uploadError($code)
    {
        $error = '';
        return JsonResponse::create([
            'state' => 0,
            'success' => false,
            'error' => $error,
            'error_code' => $code,
            'file' => '',
            'url' => '',
            'name' => '',
        ]);
    }

    /**
     * @param string $fid
     * @param string $fileName
     * @return string
     */
    protected function getFileHash($domain, $fid, $fileName)
    {
        $fileName = urldecode(htmlspecialchars_decode($fileName));
        return md5(sprintf('%s--%s--%s', $domain, $fid, $fileName));
    }

    /**
     * @param string $fid
     * @return string
     */
    protected function getFileDirectory($domain, $fid)
    {
        return base_path(sprintf('data/%s/%s/%s/', $domain, $fid[0], $fid[1]));
    }

    /**
     * @param string $fileName
     * @return string
     */
    protected function cutFileName($fileName)
    {
        $maxLength = $this->getMaxFileNameLength();
        if (mb_strlen($fileName) > $maxLength) {
            $fileExts = [];
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            while ($extension !== '') {
                $fileExts [] = '.' . $extension;
                $fileName = mb_substr($fileName, 0, mb_strlen($fileName) - mb_strlen($extension) - 1);
                $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            }
            if (empty($fileExts)) {
                $fileName = mb_substr($fileName, 0, $maxLength);
            } else {
                $extension = implode('', array_reverse($fileExts));
                $extLength = mb_strlen($extension);
                $fileName = mb_substr($fileName, 0, (int)$maxLength - $extLength) . $extension;
            }
        }
        return $fileName;
    }

    /**
     * @return int
     */
    protected function getMaxFileNameLength()
    {
        return (int)env('MAX_FILE_NAME_LENGTH', 50);
    }
}
