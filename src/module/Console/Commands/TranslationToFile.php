<?php

namespace Brotzka\TranslationManager\Module\Console\Commands;

use Illuminate\Console\Command;
use Brotzka\TranslationManager\Module\Translation;
use Brotzka\TranslationManager\Module\TranslationGroup;

class TranslationToFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:toFile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Writes all translations from database to files within the resource-folder.';

    protected $languages = [];
    protected $needed_languages = [];
    protected $translations;
    protected $translation_groups;
    protected $language_folder;
    protected $file_content;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->needed_languages = config('app.available_locales');
        $this->language_folder = resource_path('lang\\');
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->translations = Translation::all();
        $this->translation_groups = TranslationGroup::all();
	    
        $this->getAllLanguagesFromTable();
        $this->generateLanguageFolders();
        $this->generateGroupFiles();
        $this->generateFileContentArray();
        $this->distributeArrayToFiles();
    }

	/**
	 * Collects all languages from the Database
     * 
     * @return void
	 */
    protected function getAllLanguagesFromTable(): void
    {
    	foreach($this->translations  as $entry){
    		if(!in_array($entry->language, $this->languages)){
    			array_push($this->languages, $entry->language);
		    }
	    }
	    $this->compareLanguages();
    }

	/**
	 * Checks, if the languages from Database are equal to the available languages from
	 * config/app.available_locales
     * 
     * @return void
	 */
    protected function compareLanguages(): void
    {
    	foreach($this->needed_languages as $needed_language){
    		if(!in_array($needed_language, $this->languages)){
    			array_push($this->languages, $needed_language);
		    }
	    }
    }

    /**
     * Generates (if doesn't exist) all needed language folders.
     * 
     * @return void
     */
    protected function generateLanguageFolders(): void
    {
        foreach($this->languages as $language){
            if(!is_dir($this->language_folder . $language)){
                if(!mkdir($this->language_folder . $language)){
                    $this->error("Error creating folder for language: $language");
                }
                $this->info("Folder created for language: $language");
            }
        }
    }

    /**
     * Generates translation group files for each language if not exists
     * 
     * @return void
     */
    protected function generateGroupFiles(): void
    {
        foreach($this->languages as $language){
            foreach($this->translation_groups as $group){
                $file = $this->language_folder . $language . "\\" . $group->name . ".php";
                if(!file_exists($file)){
                    $new_file = fopen($file, "a");
                    fclose($new_file);
                    $this->info("Created translation group - $group->name - for language $language.");
                }
            }
        }
    }

    /**
     * Generates a structured multidimensional array containing all translations.
     */
    protected function generateFileContentArray()
    {
        $array = [];
        $array = $this->addLanguagesToArray($array);
        $array = $this->addTranslationGroupsToArray($array);
        $array = $this->addTranslationsToArray($array);
        $this->file_content = $array;
    }

    protected function distributeArrayToFiles()
    {
        foreach($this->file_content as $language => $groups){
            foreach($groups as $group => $filecontent){
                $file = $this->language_folder . $language . "\\" . $group . ".php";
                file_put_contents($file, "<?php return " . var_export($filecontent, true) . ";");
            }
        }
    }

    /**
     * Collects all relevant translations from database and writes them to the 
     * correct array-field.
     * 
     * @return array
     */
    private function addTranslationsToArray($array = []): array
    {
        foreach($array as $language => $groups){
            foreach($groups as $groupname => $entries){
                $group_model = TranslationGroup::where('name', $groupname)->first();
                $first_level_translations = Translation::where([
                    ['parent', '=', NULL],
                    ['translation_group', '=', $group_model->id],
                    ['language', '=', $language]
                ])->get();
                
                foreach($first_level_translations as $translation){
                    $array[$language][$groupname][$translation->key] = $this->getValue($language, $translation, $group_model);
                }

            }
        }
        return $array;
    }

    private function getValue($language, $translation, $group)
    {
        if(count($translation->children) > 0){
            $sub_array = [];
            foreach($translation->children as $child){   
                $sub_array[$child->key] = $this->getValue($language, $child, $group);
            }
            return $sub_array;
        } else {
            return $translation->value;
        }
    }

    /**
     * Adds translation groups to the given array.
     * 
     * @return array
     */
    private function addTranslationGroupsToArray($array = []): array
    {
        foreach(array_keys($array) as $language){
            foreach($this->translation_groups as $group){
                $array[$language][$group->name] = [];
            }
        }
        return $array;
    }

    /**
     * Adds all needed/existing languages to the given array
     * 
     * @return array
     */
    private function addLanguagesToArray($array = []): array
    {
        foreach($this->languages as $language){
            $array[$language] = [];
        }
        return $array;
    }
}
