<?php

class TesseractOCR
{
    protected $image;

    protected $language;
    protected $whitelist;
    protected $pagesegmode;

    protected $tempDir;
    protected $configFile;
    protected $outputFile;


    /***** OCR ******/

    public function recognize(){
        $this->generateConfigFile();
        $this->execute();
        $recognizedText = $this->readOutputFile();
        $this->removeTempFiles();
        return $recognizedText;
    }

    public function recognizeTxt($repertoireFichier){
        $this->executeTxt($repertoireFichier);
    }

    protected function buildWhitelistString($charLists)
    {
        $whiteList = '';
        foreach ($charLists as $list) {
            $whiteList .= is_array($list) ? join('', $list) : $list;
        }
        return $whiteList;
    }

    protected function generateConfigFile()
    {
        if ($this->whitelist) {
            $this->configFile = $this->getTempDir().rand().'.conf';
            $content = "tessedit_char_whitelist {$this->whitelist}";
            file_put_contents($this->configFile, $content);
        }
    }

    protected function execute(){
        $this->outputFile = $this->getTempDir().rand();
        exec($this->buildTesseractCommand());
    }

    protected function executeTxt($repertoireFichier){
        $this->outputFile = $repertoireFichier.$this->image;
        exec($this->buildTesseractCommand());
    }

    protected function buildTesseractCommand()
    {
        $command = "tesseract \"{$this->image}\"";

        if ($this->language) {
            $command.= " -l {$this->language}";
        }

        if ($this->pagesegmode) {
            $command.= " -psm {$this->pagesegmode}";
        }

        $command.= " {$this->outputFile}";

        if ($this->configFile) {
            $command.= " nobatch {$this->configFile}";
        }

        return $command;
    }

    /****** TEMP FILES ******/
    protected function readOutputFile(){
        $this->outputFile.= '.txt';
        return trim(file_get_contents($this->outputFile));
    }

    protected function removeTempFiles(){
        if ($this->configFile) {
            unlink($this->configFile);
        }
        unlink($this->outputFile);
    }

    /****** GETTERS SETTERS *******/

    public function setImage($image){
        $this->image = $image;
    }

    public function setPagesegMode($pagesegmode){
        $this->pagesegmode = $pagesegmode;
    }

    public function setLanguage($language){
        $this->language = $language;
    }

    public function setWhitelist(){
        $this->whitelist = $this->buildWhitelistString(func_get_args());
    }

    protected function getTempDir(){
        if (!$this->tempDir) {
            $this->tempDir = sys_get_temp_dir();
        }
        if (substr($this->tempDir, -1) != DIRECTORY_SEPARATOR) {
            $this->tempDir .= DIRECTORY_SEPARATOR;
        }
        return $this->tempDir;
    }

    public function setTempDir($path){
        $this->tempDir = $path;
    }
}
