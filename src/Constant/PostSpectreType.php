<?php
namespace Drupal\post_spectre\Constant;

class PostSpectreType
{
    const DEFAULT = 'default';
    const FULL_ISOLATION = 'full_isolation';
    const OPEN_CROSS_ORIGIN_WINDOW = 'cow';
    const CROSS_ORIGIN_OPENERS = 'coo';
    const CROSS_ORIGIN_OPENERS_IFRAME = 'coo_iframe';
    const CROSS_ORIGIN_OPENERS_POPUP = 'coo_popup';
    const OPT_OUT = 'opt_out';
    const POST_SPECTRE_TYPE = 'post_spectre_type';
}