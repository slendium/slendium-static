<?php

use Slendium\SlendiumStatic\Base\Builders\ConfigsBuilder;
use Slendium\SlendiumStatic\Base\Content\ArraySectionProvider;
use Slendium\SlendiumStatic\Base\Content\HtmlSection;
use Slendium\SlendiumStatic\Content\SectionNames;

return new ConfigsBuilder()
	->setBaseSectionProvider(new ArraySectionProvider([
		SectionNames::HEADER => new HtmlSection('Header'),
		SectionNames::FOOTER => new HtmlSection('Footer'),
	]))
	->build();
