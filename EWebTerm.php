<?php
/*
 * EWebTerm
 *
 * DESCRIPTION
 *
 *  This is a Yii framework extension that implements
 * the jQuery Wterm plugin ( http://plugins.jquery.com/project/wterm )
 *
 * @author Dimitrios Meggidis: tydeas.dr[at]gmail[dot]com
 * @link http://github.com/dmtrs/EWebTerm
 **/
class EWebTerm extends CWidget 
{
    public $htmlOptions;
    public $help;
    public $cssFile;
    public $welcome;
    public $width;
    public $height;
    public $commands;
    protected $baseUrl;
    protected $id;
    public function init() 
    {   
        $this->id = (isset($this->htmlOptions['id'])) ?
            $this->htmlOptions['id'] : 'wterm';

        $dir=dirname(__FILE__).DIRECTORY_SEPARATOR.'wterm';
        $this->baseUrl = Yii::app()->getAssetManager()->publish($dir);
        $cs = Yii::app()->clientScript;
        $cs->registerScriptFile($this->baseUrl.'/wterm.jquery.js');
        if ( $this->cssFile !== null && $this->cssFile !== false ) {
            $cs->registerCssFile($this->cssFile);
        } else {
            $cs->registerCssFile($this->baseUrl.'/wterm.css');
        }
        if ( !isset($this->welcome)) {
            $this->welcome = 'Welcome to WTerm. To Begin Using type <strong>help</strong>';
        }
        if ( !isset($this->width)) {
            $this->width = '100%';
        }
        if ( !isset($this->height)) {
            $this->height = '100%';
        }
        $cs->registerScript(
            'Yii.EWebTerm#'.$this->id,
            "$('#$this->id').wterm({".
            " WIDTH: '$this->width' , HEIGHT: '$this->height' ,".
            " WELCOME_MESSAGE: '$this->welcome' ".
            "});"
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

/*      $options=$this->getClientOptions();
        $options=$options===array()?'{}' : CJavaScript::encode($options);
        
        //$cs->registerScript('Yii.CJsTree#'.$id,"$(function () { $(\"#{$id}\").tree($options); });");
        $cs->registerScript('Yii.CJsTree#'.$id,"$(\"#{$id}\").tree($options);");

        if($this->cssFile !== null && $this->cssFile !== false)
            $cs->registerCssFile($this->cssFile); */

}
