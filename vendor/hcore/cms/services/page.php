<?php
class cmsPage {
    const TYPE                                              = "page";

    private $cms                                        	= null;
    private $contents                                       = null;
    private $css                                            = array(
        "https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
    );
    private $js                                             = array(
        "https://code.jquery.com/jquery-3.3.1.min.js"
        , "https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
    );
    private $meta                                           = null;
    private $link                                           = null;

    /**
     * cmsSchemaorg constructor.
     * @param $cms
     */
    public function __construct($cms, $params = null)
    {
        $this->cms = $cms;

        //$this->stats->setConfig($this->connectors, $this->services);
    }
    public function addContent($output) {
        if(is_array($output)) {
            $this->contents[] = $output["html"];
            if($output["css"]) {
                $this->css[] = $output["css"];
            }
            if($output["js"]) {
                $this->js[] = $output["js"];
            }
        } else {
            $this->contents[] = $output["html"];
        }
    }
    public function run() {
        $output = Cms::widget("page", array(
            "body" => array(
                "content" => $this->contents
            )
            , "css" => $this->css
            , "js" => $this->js
        ));

        echo $output;
        exit;
    }
}