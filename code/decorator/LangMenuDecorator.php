<?php
/**
 * Add Language Menu support to homepage
 * @author klemend
 *
 */
class LangMenuDecorator extends DataExtension {
    
    /**
     * Manually define locales. Use Translatable::get_allowed_locales() if none set. 
     * @var array
     */
    private static $all_allowed_locales = [];
    
    /**
     * Default classes we use if we don't find translation
     * @var array
     */
    private static $default_classes = [
        'HomePage',
        'Page',
    ];
    
    private static $search_for_translation = true;
    
    private static $hidden_locales = [];
    
    
    /**
     * Build Language Menu
     * @return ArrayList
     */
    public function LangMenu() {
        $all_allowed_locales = Config::inst()->get('LangMenuDecorator', 'hidden_locales');
        if( !$all_allowed_locales || !count($all_allowed_locales) ) {
            $all_allowed_locales = Translatable::get_allowed_locales();
        }
        
        $hidden_locales = Config::inst()->get('LangMenuDecorator', 'hidden_locales');
        $search_for_translation = Config::inst()->get('LangMenuDecorator', 'search_for_translation');
        $default_classes = Config::inst()->get('LangMenuDecorator', 'default_classes');
        $current_locale = $this->owner->Locale;
        $out_list = ArrayList::create();
        
        if( $this->owner->hasMethod('onBeforeLangMenu')) {
            $this->owner->onBeforeLangMenu($all_allowed_locales);
        }
        
        foreach($all_allowed_locales as $locale){
            
            // if we try to hide locale
            if( count($hidden_locales) && in_array($locale, $hidden_locales) ) {
                continue;
            }
            
            $current = [
                'Locale'    => $locale,
                'i18n'      => [
                    'Lang'          => i18n::get_lang_from_locale($locale),
                    'Locale'        => i18n::get_locale_name($locale),
                    'Language'      => i18n::get_language_name($locale, false),
                    'LanguageNative'=> i18n::get_language_name($locale, false),
                ],
                'Selected'  => $this->owner->Locale == $locale,
            ];
            
            $page = false;
            if( $search_for_translation ) {
                if( $this->owner->Locale == $locale ) {
                    $page = $this->owner;
                } else if( $this->owner->hasTranslation($locale) ) {
                    $page = $this->owner->getTranslation($locale);
                }
            }
            
            if( !$search_for_translation || !$page ) {
                foreach( $default_classes as $class ) {
                    Translatable::set_current_locale($locale);
                    $page = $class::get()->first();
                    if( $page ) {
                        break;
                    }
                }
                Translatable::set_current_locale($current_locale);
            }
            
            $current['Page'] = $page;
            
            if( $this->owner->hasMethod('onBeforePushLangMenu')) {
                $this->owner->onBeforePushLangMenu($current);
            }
            
            $out_list->push( $current );
        }
        
        if( $this->owner->hasMethod('onAfterLangMenu')) {
            $this->owner->onAfterLangMenu($out_list);
        }
        
        return $out_list;
    }
    
    
}