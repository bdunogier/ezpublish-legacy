{section show=$attribute.content.has_discount}
{"Price:"|i18n("design/standard/content/datatype")} {$attribute.content.inc_vat_price|l10n(currency)}
{"Your price:"|i18n("design/standard/content/datatype")} {$attribute.content.discount_price_inc_vat|l10n(currency)}
{"You save:"|i18n("design/standard/content/datatype")} {sub($attribute.content.inc_vat_price,$attribute.content.discount_price_inc_vat)|l10n(currency)} ( {$attribute.content.discount_percent} % )
{section-else}
{"Price"|i18n("design/standard/content/datatype")} {$attribute.content.inc_vat_price|l10n(currency)}
{/section}