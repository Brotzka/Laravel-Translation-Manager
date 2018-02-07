<?php

namespace Brotzka\TranslationManager\Module\Console\Commands;

use Illuminate\Console\Command;
use Brotzka\TranslationManager\Module\Translation;
use Brotzka\TranslationManager\Module\TranslationGroup;

class TranslationToDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:toDatabase';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extracts all language-files and writes the content to the database';

    /**
     * Other variables
     */
    private $lang_folder;
	private $language;
	private $languages = [];
    private $translation_groups = [];

    private $stats = ['counter' => 0, 'groups' => []];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
		$this->lang_folder = resource_path('lang/');
		$this->languages = config('app.available_locales');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		$this->language = config('app.locale');
        
        $this->getTranslationGroups($this->language);

	    	// Translation-Groups loopen
		    foreach($this->translation_groups as $group){
		    	$file = $this->lang_folder . $this->language . '/' . $group->name . '.php';
				if(!file_exists($file)){
					$this->error("Cannot open file: " . $file);
					continue;
				}
		    	$contents = require_once($file);
				if(!is_array($contents)){
					$this->error("File: " . $file . " does not contain an array! File will be scipped!");
					continue;
				}
		    	$this->getValue($contents, $this->languages, $group);
		    }
    }

	/**
	 * Writes all translation to database.
	 * Updates values, if they already exist.
	 * Keeps care of multidimensional arrays.
	 * @param array $values
	 * @param $language
	 * @param $group
	 * @param null $parent
	 */
    private function getValue(array $values, $languages, $group, $parent = NULL)
    {
    	foreach($values as $key => $value){

			foreach($languages as $language){
				$entry = Translation::firstOrCreate([
					'key' => $key,
					'language' => $language,
					'translation_group' => $group->id,
					'parent' => $parent
				]);
				if(is_array($value)){
					$this->getValue($value, $languages, $group, $entry->id);
				} else {
					if($entry->value === NULL){
						$entry->value = $value;
						$entry->save();
					}
				}
			}
	    }
    }


	/**
	 * Extracts all files from the "master" language and takes them as translation groups.
	 * @param string $lang
	 *
	 * @return void
	 */
    private function getTranslationGroups($lang = 'en'): void
    {
        $dir = resource_path('lang/'. $lang . '/');
        $files = array_diff(scandir($dir), array('..', '.'));

        foreach($files as $index => $filename){
            $groupname = str_replace(".php", "", $filename);
            $this->updateTranslationGroups($groupname);
        }

    }

	/**
	 * Creates new translation-group if it doesn't exists already.
	 * @param $groupname
	 */
    private function updateTranslationGroups($groupname)
    {
    	$group = TranslationGroup::firstOrCreate(['name' => $groupname]);
    	array_push($this->translation_groups, $group);
    }
}
