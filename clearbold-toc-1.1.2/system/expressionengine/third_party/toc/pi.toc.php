<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
  'pi_name' => 'Table of Contents',
  'pi_version' =>'1.1.2',
  'pi_author' =>'Mark J. Reeves / Clearbold, LLC',
  'pi_author_url' => 'http://www.clearbold.com/',
  'pi_description' => 'A plugin that parses HTML for heading tags (H3 or specified) and generates a table of contents with jump links. Upgrade to the Pro version for nested headings and more.',
  'pi_usage' => toc::usage()
  );

class toc {

    public  $return_data = '';
    var $heading;

    /**
     * Constructor
     *
     *
     *
     * @access public
     * @return void
     */
    public function toc()
    {
        $this->EE =& get_instance();

        if ( trim($this->EE->TMPL->tagdata) == '')
            { return; }

        $this->heading = $this->EE->TMPL->fetch_param('heading', 'h3');

        $return_data = '<ul class="ul-toc">';

        $dom = new DOMDocument;
        $dom->loadHTML(utf8_decode(trim($this->EE->TMPL->tagdata)));
        $xpath = new DOMXPath($dom);
        $nodes = $xpath->query('//*[self::'.$this->heading.']');
        $i = 0;
        foreach( $nodes as $node ) {
            $i++;
            $return_data .= "\n".'<li><a href="#heading' . $i . '">' . trim($node->nodeValue) . '</a></li>';
        }

        $return_data .= "\n</ul>";

        // return
        $this->return_data = $return_data;
    }
    public function article()
    {

        if ( trim($this->EE->TMPL->tagdata) == '')
            { return; }

        $clean_html = strtr(trim($this->EE->TMPL->tagdata), array(
            '&quot;' => '&#34;',
            '&amp;' =>  '&#38;',
            '&apos;' => '&#39;',
            '&lt;' =>   '&#60;',
            '&gt;' =>   '&#62;',
            '&nbsp;' => '&#160;',
            '&copy;' => '&#169;',
            '&laquo;' => '&#171;',
            '&reg;' =>   '&#174;',
            '&raquo;' => '&#187;',
            '&trade;' => '&#8482;',
            '&rdquo;' => '&#8221;',
            '&ldquo;' => '&#8220;',
            '&rsquo;' => '&#8217;',
            '&lsquo;' => '&#8216;'
          ));

        $dom = new DOMDocument;
        //$dom->loadHTML(utf8_decode($this->EE->TMPL->tagdata));
        $dom->loadHTML($clean_html);
        $xpath = new DOMXPath($dom);
        $nodes = $xpath->query('//*[self::'.$this->heading.']');
        $i = 0;
        foreach( $nodes as $node ) {
            $i++;
            $domAttribute = $dom->createAttribute('id');
            $domAttribute->value = 'heading' . $i;
            $node->appendChild($domAttribute);
        }

        $html_fragment = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace( array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $dom->saveHTML()));
        $return_data = $html_fragment;

        // return
        return $return_data;
    }

    // usage instructions
    public function usage()
    {
        ob_start();
?>
-------------------
HOW TO USE
-------------------
{exp:toc}{tag output containing HTML}{/exp:toc}

The {exp:toc} tag will output the table of contents as an unordered list of #links, with a class of "ul-toc".

{exp:toc:article}{tag output containing HTML}{/exp:toc:article}

The {exp:toc:article} tag will output the original content, with all specified headings updated with corresponding IDs.

The default heading parsed is H3. You can specify a heading tag of your choice as:

{exp:toc heading="h2"}{tag output containing HTML}{/exp:toc}
{exp:toc:article heading="h2"}{tag output containing HTML}{/exp:toc:article}


    <?php
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }
}

/* End of file pi.toc.php */
/* Location: ./system/expressionengine/third_party/toc/pi.toc.php */