<?php
class Config {
	public $SERVERNAME = "localhost"; 
	public $DATABASE_USERNAME = "root"; 
	public $DATABASE_PASSWORD = "12345678"; //
	public $DATABASE_NAME = "msr";
	public $MAIL_SENDER = "gaalp@cellkabel.hu";
	public $MAIL_USERNAME = "gaalp";
	public $MAIL_PASSWORD = "VasCsigaPok";
	public $MAIL_SMTP = "mail.cellkabel.hu";
	public $SALT = "SZMSZC553334411212";
	public $DAY_REMAINING_TO_VALID = 30;
	public $LOGIN_PROBE = 3;
	public $BASE_URL = "localhost/msr";
	public $BUSS_NAME = "Honvéd Ezüst Nyíl Sportegyesület";

	public $SUCCESS_REGISTRATION_SUBJECT = "Sikeres regisztráció";
	public $SUCCESS_REGISTRATION_MESSAGE = "A regisztráció sikerült.";

	public $THUMB_IMAGE_HEIGHT = 200;
	public $IMAGE_HEIGHT = 500;

	public $ROOT_FOLDER;
	public $SITE_NAME = "MSR";
	public $SITE_ROOT = "/beta";
	public $UPLOAD_FOLDER = "/uploads";
	public $ABSOLUTE_UPLOAD_FOLDER;

	public $FILE_COVER = array();
	public $NO_UPLOADED_IMAGE;
    public $MAX_FILE_SIZE = 10;		// file size in MB

    public $LINKED_TO_DEFAULT = "main";
    public $LANG_DEFAULT;
    public $LANGUAGE = array("hu"=>"Magyar","en"=>"Angol","de"=>"Német"); 

    public function __construct() {
    		$this->ROOT_FOLDER 				= $_SERVER['DOCUMENT_ROOT'].$this->SITE_ROOT;
			$this->ABSOLUTE_UPLOAD_FOLDER 	= $this->ROOT_FOLDER.'/uploads';
			$this->NO_UPLOADED_IMAGE 		= $this->SITE_ROOT."/uploads/common/no_image_16_10.png";
			$this->LANG_DEFAULT 			= key($this->LANGUAGE);
			
			$this->FILE_COVER["doc"]["icon"] 	= $this->SITE_ROOT.$this->UPLOAD_FOLDER."/common/doc.png";
			$this->FILE_COVER["doc"]["mime"]	= "application/msword";

			$this->FILE_COVER["rtf"]["icon"] 	= $this->SITE_ROOT.$this->UPLOAD_FOLDER."/common/doc.png";
			$this->FILE_COVER["rtf"]["mime"]	= "application/msword";

			$this->FILE_COVER["docx"]["icon"] 	= $this->SITE_ROOT. $this->UPLOAD_FOLDER."/common/docx.png";
			$this->FILE_COVER["docx"]["mime"]	= "application/vnd.openxmlformats-officedocument.wordprocessingml.document";

			$this->FILE_COVER["xls"]["icon"] 	= $this->SITE_ROOT.$this->UPLOAD_FOLDER."/common/xls.png";
			$this->FILE_COVER["xls"]["mime"] 	= "application/vnd.ms-excel";

			$this->FILE_COVER["xlsx"]["icon"] 	= $this->SITE_ROOT.$this->UPLOAD_FOLDER."/common/xlsx.png";
			$this->FILE_COVER["xlsx"]["mime"]	= "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";

			$this->FILE_COVER["pdf"]["icon"] 	= $this->SITE_ROOT.$this->UPLOAD_FOLDER."/common/pdf.png";
			$this->FILE_COVER["pdf"]["mime"]	= "application/pdf";

			$this->FILE_COVER["ppt"]["icon"] 	= $this->SITE_ROOT.$this->UPLOAD_FOLDER."/common/ppt.png";
			$this->FILE_COVER["ppt"]["mime"]	= "application/vnd.ms-powerpoint";

			$this->FILE_COVER["pptx"]["icon"] 	= $this->SITE_ROOT.$this->UPLOAD_FOLDER."/common/pptx.png";
			$this->FILE_COVER["pptx"]["mime"]	= "application/vnd.ms-powerpoint";

			$this->FILE_COVER["rar"]["icon"] 	= $this->SITE_ROOT.$this->UPLOAD_FOLDER."/common/rar.png";
			$this->FILE_COVER["rar"]["mime"]	= "application/x-rar-compressed";

			$this->FILE_COVER["zip"]["icon"] 	= $this->SITE_ROOT.$this->UPLOAD_FOLDER."/common/zip.png";
			$this->FILE_COVER["zip"]["mime"]	= "application/zip";

			$this->FILE_COVER["jpg"]["icon"] 	= $this->SITE_ROOT.$this->UPLOAD_FOLDER."/common/xlsx.png";
			$this->FILE_COVER["jpg"]["mime"]	= "image/jpeg";

			$this->FILE_COVER["jpeg"]["icon"] 	= $this->SITE_ROOT.$this->UPLOAD_FOLDER."/common/xlsx.png";
			$this->FILE_COVER["jpeg"]["mime"]	= "image/jpeg";

			$this->FILE_COVER["png"]["icon"] 	= $this->SITE_ROOT.$this->UPLOAD_FOLDER."/common/xlsx.png";
			$this->FILE_COVER["png"]["mime"]	= "image/png";

			
	}		
}
?>