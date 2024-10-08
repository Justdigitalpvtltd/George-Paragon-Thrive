<?php

/**
 * Created by PhpStorm.
 * User: radu
 * Date: 22.01.2015
 * Time: 11:50
 */
class Thrive_Ult_Others_Tab extends Thrive_Ult_Tab {
	/** @var  Thrive_Ult_Visitors_Status_Tab */
	protected $visitor_status;

	/** @var  Thrive_Ult_Direct_Urls_Tab */
	protected $direct_urls;

	public function __construct() {
		$this->visitor_status = new Thrive_Ult_Visitors_Status_Tab();
		$this->direct_urls    = new Thrive_Ult_Direct_Urls_Tab();

		$this->direct_urls->setExclusions( $this->visitor_status->getItems() );

		parent::__construct();
	}

	/**
	 * Specific tab has to implement this function which transforms
	 * items(pages, posts, post types) into Option models
	 *
	 * @return array
	 */
	protected function matchItems() {
		$this->visitor_status->matchItems();
		$this->direct_urls->matchItems();

		$this->options = array_merge(
			$this->visitor_status->options,
			$this->direct_urls->options
		);

		return $this;
	}

	/**
	 * Has to get the Option from json string based on the $item
	 *
	 * @param $item
	 *
	 * @return Option
	 */
	protected function getSavedOption( $item ) {
		return new Thrive_Ult_Option();
	}

	/**
	 * Read items from the database and initiate them
	 *
	 * @return $this
	 */
	protected function initItems() {
		$this->visitor_status->getItems();
		$this->direct_urls->getItems();

		return $this;
	}

	public function setHanger( $hanger ) {
		$this->hanger = $hanger;
		$this->visitor_status->setHanger( $hanger );
		$this->direct_urls->setHanger( $hanger );

		return $this;
	}

	/**
	 * @param string $campaign_id
	 *
	 * @return $this
	 */
	public function setGroup( $campaign_id ) {
		$this->campaign_id = $campaign_id;
		$this->visitor_status->setGroup( $campaign_id );
		$this->direct_urls->setGroup( $campaign_id );

		return $this;
	}

	/**
	 * set the options individually for the 2 subtabs from here
	 *
	 * @param Thrive_Ult_Campaign_Options $savedOptions
	 *
	 * @return $this
	 */
	public function setSavedOptions( Thrive_Ult_Campaign_Options $savedOptions ) {
		$this->visitor_status->setSavedOptions( $savedOptions );
		$this->direct_urls->setSavedOptions( $savedOptions );

		return parent::setSavedOptions( $savedOptions );
	}


}
