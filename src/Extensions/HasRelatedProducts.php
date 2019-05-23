<?php

namespace AntonyThorpe\SilverShopRelatedProducts;

use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\FieldList;
use SilverShop\Page\Product;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Versioned\GridFieldArchiveAction;
use SilverStripe\Forms\TextField;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;

/**
 * Can be applied to any buyable to add the related product feature.
 *
 * @link(https://github.com/dynamic/silverstripe-products/blob/4866c6a677d560fef4e7eee8b435f2b7533ff158/src/Extension/RelatedProductsDataExtension.php)
 */
class HasRelatedProducts extends DataExtension
{
    private static $many_many = [
        'RelatedProductsRelation' => Product::class,
    ];

    private static $many_many_extraFields = [
        'RelatedProductsRelation' => [
            'RelatedOrder' => 'Int',
            'RelatedTitle' => 'Varchar'
        ]
    ];

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        if ($this->owner->ID) {
            $fields->addFieldsToTab('Root.' . _t(__CLASS__ . '.Related', 'Related'), [
                $grid = GridField::create(
                    'RelatedProductsRelation',
                    _t(
                        __CLASS__ . '.RelatedProductsRelation',
                        'Related Products'
                    ),
                    $this->owner->RelatedProductsRelation()->sort('RelatedOrder', 'ASC'),
                    $relatedConfig = GridFieldConfig_RelationEditor::create()
                        ->addComponent(new GridFieldEditableColumns(), GridFieldEditButton::class)
                        ->removeComponentsByType([
                            GridFieldAddNewButton::class,
                            GridFieldEditButton::class,
                            GridFieldArchiveAction::class
                        ])
                )->setDescription(
                    _t(__CLASS__ . '.Description', 'Link related products using the search field top right and then add a title for this related product.  Drag and drop to reorder.')
                )
            ]);

            // Add RelatedTitle to GridField
            $grid->getConfig()->getComponentByType(GridFieldEditableColumns::class)->setDisplayFields([
                'RelatedTitle' => function ($record, $column, $grid) {
                    return new TextField($column);
                }
            ]);

            // Format the autocomplete search for a product to link
            $relatedConfig->getComponentByType(GridFieldAddExistingAutocompleter::class)
                ->setSearchFields(array('InternalItemID', 'Title'))
                ->setResultsFormat('$InternalItemID - $Title');

            // Add reorder capabilities when more than two items
            if ($this->owner->RelatedProductsRelation()->count() > 1) {
                $relatedConfig->addComponent(GridFieldOrderableRows::create('RelatedOrder')/*->setRepublishLiveRecords(true)*/);
                // @todo uncomment post symbiote/silverstripe-gridfieldextensions:3.2.1
            }
        }
    }

    /**
     * @param int $limit [optional]
     * @param bool $random [optional]
     * @return \SilverStripe\ORM\SS_List
     */
    public function getRelatedProducts($limit = null, $random = false)
    {
        $related_products = $this->owner->RelatedProductsRelation();

        if ($random) {
            $related_products = $related_products->sort("RAND()");
        } else {
            $related_products = $related_products->sort('RelatedOrder');
        }

        if ($limit !== null) {
            $related_products = $related_products->limit($limit);
        }

        $this->owner->extend('updateRelatedProducts', $related_products, $limit, $random);

        return $related_products;
    }

    /**
     * Cleanup
     */
    public function onBeforeDelete()
    {
        $this->owner->RelatedProductsRelation()->removeAll();
    }
}
