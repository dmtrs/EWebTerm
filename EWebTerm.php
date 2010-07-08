<?php
/**
 * EWebTerm
 *
 * DESCRIPTION
 *
 *  This is a Yii framework extension that implements
 * the jQuery Wterm plugin ( http://plugins.jquery.com/project/wterm )
 *
 *
 * @version 0.15
 * @author  Dimitrios Meggidis <tydeas.dr@gmail.com>
 * @link    http://github.com/dmtrs/EWebTerm
 **/

class EWebTerm extends CWidget
{
    public $htmlOptions;
    public $cssFile;
    public $commands;
    public $properties;
    protected $baseUrl;
    protected $id;
    protected $avprop = array(
        'PS1',                //Primary Prompt ( Defaults to 'wterm $' )
        'TERMINAL_CLASS',     //Class name that apply to terminal Container ( Defaults to 'wterm_terminal' )
        'PROMPT_CLASS',       //Class name that apply to prompt ( Defaults to 'wterm_prompt' )
        'THEME_CLASS_PREFIX', //Theme Class Name that will be prefixed to themes for example theme 'subbeam' will
                      //automatically become 'prefix_subbeam'
        'DEFAULT_THEME',   //Default Theme Class Name defaults to 'green_on_black'
        'HIGHLIGHT_CLASS', //Class name to apply to highlighted text
        'KEYWORD_CLASS',   //Class name to apply to keywords
        'WIDTH',           //Width for container
        'HEIGHT',          //Height for container
        'WELCOME_MESSAGE', //Default Welcome Message to show when the terminal is first initialized
        'NOT_FOUND',       //Message to show when a command is not found
                   //'CMD' in this string will automatically be
                   //translated to command user typed in
        'HELP',            //Help not finalized
        'AUTOCOMPLETE',    //Boolean, Autocomplete enabled/disabled ( By Default Enabled )
        'HISTORY',         //Boolean, History enabled/disabled ( By Default Enabled )
        'AJAX_METHOD',     // HTTP Method to call must be GET/POST ( Defaults to GET )
        'AJAX_PARAM'       //Parameter to send the command in for HTTP request ( Default to tokens )
    );

    public function init() 
    { 
        $this->id = (isset($this->htmlOptions['id'])) ?
            $this->htmlOptions['id'] : 'wterm';
        $jsen = array();

        $dir=dirname(__FILE__).DIRECTORY_SEPARATOR.'wterm';
        $this->baseUrl = Yii::app()->getAssetManager()->publish($dir);
        $cs = Yii::app()->clientScript;
        $cs->registerScriptFile($this->baseUrl.'/wterm.jquery.js');
        if ( $this->cssFile !== null && $this->cssFile !== false ) {
            $cs->registerCssFile($this->cssFile);
        } else {
            $cs->registerCssFile($this->baseUrl.'/wterm.css');
        }

        if ( isset($this->properties) ) {
            $prs = array_change_key_case($this->properties, CASE_UPPER);
            $this->avprop = array_flip($this->avprop);
            $prs = array_intersect_key($prs, $this->avprop);
    
            $jsen = CJavaScript::encode($prs);

        }
        $ops = (!empty($jsen)) ? $jsen : '{}' ;

        $cs->registerScript(
            'Yii.EWebTerm#'.$this->id,
            "$('#$this->id').wterm($ops);"
        );
        $this->registerCommands($cs);
    }

    public function run()
    {
        echo CHtml::tag('div', array('id'=>$this->id));
    }

    protected function registerCommands( $cs )
    {
        foreach ( $this->commands as $name => $attr ) {
            $cs->registerScript(
                $this->id."_".$name,
                "$.register_command( '$name' , ".$attr['return'].");",
                CClientScript::POS_END
            );
        }
    }
}
