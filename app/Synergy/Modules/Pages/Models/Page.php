<?php

namespace Synergy\Modules\Pages\Models;

class Page extends \Models\BaseModel
{
    protected $table = 'synergy_pages';
    
    protected $primaryKey = 'page_id';
    
    const CREATED_AT = 'page_created';
    const UPDATED_AT = 'page_updated';
    
    /* */
    
    public function tags()
    {
        return $this->belongsToMany(
            '\\Models\\Site\\Tag',
            'synergy_page_tag_links',
            'page_id',
            'tag_id'
        )->orderBy('tag_name', 'ASC');
    }
    
    public function files()
    {
        return $this->hasMany('\\Synergy\\Modules\\Pages\\Models\\PageFile', 'file_page_id');
    }
    
    public function module()
    {
        return $this->hasOne('\\Models\\CMS\\Module\\Registration', 'module_page_id');
    }
    
    public function parent()
    {
        return $this->belongsTo('\\Synergy\\Modules\\Pages\\Models\\Page', 'page_parent_id');
    }
    
    public function children()
    {
        return $this->hasMany('\\Synergy\\Modules\\Pages\\Models\\Page', 'page_parent_id')->orderBy('page_order', 'asc');
    }
    
    public function activeChildren()
    {
        return $this->children()->where('page_active', '=', 1);
    }
    
    public function navChildren()
    {
        return $this->activeChildren()->where('page_nav', '=', 1);
    }
    
    public function footerChildren()
    {
        return $this->activeChildren()->where('page_footer', '=', 1);
    }
    
    /* */
    
    public static function conflictingURL($url)
    {
        return (static::where('page_url', '=', $url)->count() != 0);
    }
    
    /* */

    protected function deleteImages($file = null)
    {
        if (func_num_args() == 0) {
            $file = $this->page_image;
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
        $this->tags()->detach();

        \Models\Site\Tag::pruneUnusedTags();

        $delete_file = $this->page_image;
        
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
        $original_image = $this->getOriginal('page_image');

        $return = parent::save($options);

        if ($original_image && $original_image != $this->page_image) {
            $this->deleteImages($original_image);
        }
        
        return $return;
    }
    
    /* */
    
    public function getNextOrder()
    {
        $last_order = (int)\DB::table('synergy_pages')
            ->orderBy('page_order', 'desc')
            ->limit(1)
            ->pluck('page_order');
        
        return ($last_order + 1);
    }
    
    //
    
    public function reorderEntriesSequentially()
    {
        $pages = static::orderBy('page_order', 'asc')->get();
        
        $position = 1;
        
        foreach ($pages as $page) {
            $page->page_order = $position;
            
            $page->save();
            
            ++$position;
        }
    }
}