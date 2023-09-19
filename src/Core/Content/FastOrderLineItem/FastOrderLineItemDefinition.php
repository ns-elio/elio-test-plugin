<?php declare(strict_types=1);

namespace Elio\TestPlugin\Core\Content\FastOrderLineItem;

use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class FastOrderLineItemDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'elio_fast_order_line_item';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return FastOrderLineItemCollection::class;
    }

    public function getEntityClass(): string
    {
        return FastOrderLineItemEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new DateTimeField('dateTime', 'dateTime'))->addFlags(new Required()),
            (new StringField('sessionId', 'sessionId'))->addFlags(new Required()),
            (new IntField('quantity', 'quantity'))->addFlags(new Required()),
            (new LongTextField('comment', 'comment'))->addFlags(),
            (new ReferenceVersionField(ProductDefinition::class, 'product_version_id'))->addFlags(new Required()),
            (new FkField('product_id', 'productId', ProductDefinition::class))->addFlags(new Required()),

            (new ManyToOneAssociationField('product','product_id', ProductDefinition::class, 'id'))->addFlags(new Required()),
        ]);
    }
}