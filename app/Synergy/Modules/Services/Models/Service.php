<?php

namespace Synergy\Modules\Services\Models;

class Service extends \Models\BaseModel
{
    protected $table = 'synergy_services';
    
    protected $primaryKey = 'service_id';
    
    const CREATED_AT = 'service_created';
    const UPDATED_AT = 'service_updated';
    
    /* */
    
    public function tags()
    {
        return $this->belongsToMany(
            '\\Models\\Site\\Tag',
            'synergy_service_tag_links',
            'service_id',
            'tag_id'
        )->orderBy('tag_name', 'ASC');
    }
    
    public function files()
    {
        return $this->hasMany('\\Synergy\\Modules\\Services\\Models\\ServiceFile', 'file_service_id');
    }
    
    public function categories()
    {
        return $this->belongsToMany(
            '\\Synergy\\Modules\\Services\\Models\\ServiceCategory',
            'synergy_service_category_links',
            'service_id',
            'category_id'
        );
    }

    /* */
    
    protected function deleteImages($file = null)
    {
        if (func_num_args() == 0) {
            $file = $this->service_image;
        }

        if ($file) {
            $directory = \Config::get('synergy.uploads.images.directory');

            \File::delete("{$directory}{$file}");
            \File::delete("{$directory}resize/{$file}");
            \File::delete("{$directory}square/{$file}");
            \File::delete("{$directory}thumb/{$file}");
        }
    }
    
    // 
    
    public function delete()
    {
        $this->categories()->detach();
        
        $this->tags()->detach();
            
        \Models\Site\Tag::pruneUnusedTags();
        
        $delete_file = $this->service_image;
        
        $return = parent::delete();

        $this->deleteImages($delete_file);
        
        //
        
        foreach ($this->files as $file) {
            $file->delete();
        }
        
        // 
        
        $this->reorderEntriesSequentially();
        
        //

        return $return;
    }
    
    public function save(array $options = array())
    {        
        $original_image = $this->getOriginal('service_image');

        $return = parent::save($options);

        if ($original_image && $original_image != $this->service_image) {
            $this->deleteImages($original_image);
        }
        
        return $return;
    }
    
    // 
    
    public function getNextOrder()
    {
        $last_order = (int)\DB::table('synergy_services')
            ->orderBy('service_order', 'desc')
            ->limit(1)
            ->pluck('service_order');
        
        return ($last_order + 1);
    }

    //
    
    public function reorderEntriesSequentially()
    {
        $services = static::orderBy('service_order', 'asc')->get();
        
        $position = 1;
        
        foreach ($services as $service) {
            $service->service_order = $position;
            
            $service->save();
            
            ++$position;
        }
    }
    
    /* */
    
    public function getRelatedServices($limit = 0)
    {
        $categories = $this->categories->lists('category_id');
        
        if (!empty($categories)) {
            $query = static::orderBy('service_order', 'asc')
                ->where('service_id', '!=', $this->service_id)
                ->where('service_active', '=', 1)
                ->where(\DB::raw('0'), '<', function($query) use ($categories)
                {
                    $query->select(\DB::raw('COUNT(*)'))
                            ->from('synergy_service_category_links')
                            ->where('service_id', '=', \DB::raw('`synergy_services`.`service_id`'))
                            ->whereIn('category_id', $categories);
                });

            if ($limit) {
                $query->limit($limit);
            }

            return $query->get();
        } else {
            return null;
        }
    }
}