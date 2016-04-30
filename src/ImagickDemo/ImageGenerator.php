<?php


namespace ImagickDemo;


use Room11\HTTP\Body;
use Room11\HTTP\VariableMap;
use Room11\HTTP\HeadersSet;
use ImagickDemo\Helper\PageInfo;
use ImagickDemo\Navigation\CategoryInfo;
use ImagickDemo\Queue\ImagickTaskQueue;
use ImagickDemo\Config;
use Predis\Client as RedisClient;
use Psr\Http\Message\ServerRequestInterface as Request;
use Room11\HTTP\Body\BlobBody;
use Room11\HTTP\Body\TextBody;
use Tier\Body\CachingFileBodyFactory;
use Tier\Bridge\RouteParams;

class ImageGenerator
{
    public function __construct(ImageCachePath $imageCachePath)
    {
        $this->imageCachePath = $imageCachePath;
    }

    public static function createImageTask(
        VariableMap $variableMap,
        ImagickTaskQueue $taskQueue,
        PageInfo $pageInfo,
        Request $request,
        HeadersSet $headersSet,
        $customImage,
        $params
    ) {
        $job = $variableMap->getVariable('job', false);
        
        $text = "Image is still generating.";
        if ($job === false) {
            if ($taskQueue->isActive() == false) {
                //Queue isn't active - don't bother queueing a task
                return false;
            }
    
            $task = new \ImagickDemo\Queue\ImagickTask(
                $pageInfo,
                $params,
                $customImage,
                $request->getUri()->getPath()
            );
            $added = $taskQueue->addTask($task);
            
            if ($added === true) {
                $text = "Image added to task list";
            }
            else {
                $text = "Image task $added already present.";
            }
        }
    
        $caching = new \Room11\Caching\LastModified\Disabled();
        foreach ($caching->getHeaders(time()) as $key => $value) {
            $headersSet->addHeader($key, $value);
        }
    
        return new TextBody($text, 420);
    }

    
    public function directImageCallable(
        PageInfo $pageInfo,
        \Auryn\Injector $injector,
        RouteParams $routeInfo,
        $params
    ) {
        App::setupCategoryExample($routeInfo);
        $imageFunction = CategoryInfo::getImageFunctionName($pageInfo);

        global $imageType;
        ob_start();
        $injector->execute($imageFunction);
    
        if ($imageType == null) {
            ob_end_clean();
            throw new \Exception("imageType not set, can't cache image correctly.");
        }
        $imageData = ob_get_contents();
        ob_end_clean();

        $simpleNameWithExtension = $pageInfo->getSimpleName($params).'.'.$imageType;

        return new BlobBody($simpleNameWithExtension, $imageData, "image/".$imageType);
    }

    public function directCustomImageCallable(
        PageInfo $pageInfo,
        RouteParams $routeInfo,
        \Auryn\Injector $injector,
        $params
    ) {
        App::setupCategoryExample($routeInfo);

        $imageFunction = CategoryInfo::getCustomImageFunctionName($pageInfo);

        global $imageType;
    
        ob_start();
        $injector->execute($imageFunction);
    
        if ($imageType == null) {
            ob_end_clean();
            throw new \Exception("imageType not set, can't cache image correctly.");
        }
        $imageData = ob_get_contents();
    
        ob_end_clean();
        
        $simpleNameWithExtension = $pageInfo->getSimpleName($params).'.'.$imageType;

        return new BlobBody($simpleNameWithExtension, $imageData, "image/".$imageType);
    }
    
    public function cachedImageCallable(
        PageInfo $pageInfo,
        CachingFileBodyFactory $fileBodyFactory,
        $params
    ) {
        $filename = $this->imageCachePath->getImageCacheFilename($pageInfo, $params);
        $extensions = ["jpg", 'jpeg', "gif", "png", ];
        $contentType = false;
        $filenameFound = false;
        $extension = null;
    
        foreach ($extensions as $extension) {
            $filenameWithExtension = $filename.".".$extension;
            if (file_exists($filenameWithExtension) == true) {
                //TODO - content type should actually be image/jpeg
                $contentType = "image/".$extension;
                $filenameFound = $filenameWithExtension;
                break;
            }
        }
    
        if ($filenameFound == false || $extension == null) {
            return false;
        }

        $simpleNameWithExtension = $pageInfo->getSimpleName($params).'.'.$extension;
    
        return $fileBodyFactory->create($filenameFound, $simpleNameWithExtension, $contentType);
    }
}
