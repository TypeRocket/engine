<?php
namespace TypeRocket\Engine7\Utility;

use TypeRocket\Engine7\Elements\Traits\Attributes;

/**
 * Class Tag
 *
 * @method static Html div(mixed ...$arguments)
 * @method static Html sup(mixed ...$arguments)
 * @method static Html sub(mixed ...$arguments)
 * @method static Html abbr(mixed ...$arguments)
 * @method static Html address(mixed ...$arguments)
 * @method static Html area(mixed ...$arguments)
 * @method static Html audio(mixed ...$arguments)
 * @method static Html base(mixed ...$arguments)
 * @method static Html canvas(mixed ...$arguments)
 * @method static Html header(mixed ...$arguments)
 * @method static Html main(mixed ...$arguments)
 * @method static Html menu(mixed ...$arguments)
 * @method static Html bdi(mixed ...$arguments)
 * @method static Html bdo(mixed ...$arguments)
 * @method static Html menuitem(mixed ...$arguments)
 * @method static Html code(mixed ...$arguments)
 * @method static Html em(mixed ...$arguments)
 * @method static Html label(mixed ...$arguments)
 * @method static Html legend(mixed ...$arguments)
 * @method static Html i(mixed ...$arguments)
 * @method static Html strong(mixed ...$arguments)
 * @method static Html b(mixed ...$arguments)
 * @method static Html pre(mixed ...$arguments)
 * @method static Html section(mixed ...$arguments)
 * @method static Html nav(mixed ...$arguments)
 * @method static Html head(mixed ...$arguments)
 * @method static Html data(mixed ...$arguments)
 * @method static Html title(mixed ...$arguments)
 * @method static Html col(mixed ...$arguments)
 * @method static Html colgroup(mixed ...$arguments)
 * @method static Html source(mixed ...$arguments)
 * @method static Html article(mixed ...$arguments)
 * @method static Html datalist(mixed ...$arguments)
 * @method static Html dd(mixed ...$arguments)
 * @method static Html dl(mixed ...$arguments)
 * @method static Html dt(mixed ...$arguments)
 * @method static Html span(mixed ...$arguments)
 * @method static Html button(mixed ...$arguments)
 * @method static Html li(mixed ...$arguments)
 * @method static Html ul(mixed ...$arguments)
 * @method static Html ol(mixed ...$arguments)
 * @method static Html select(mixed ...$arguments)
 * @method static Html option(mixed ...$arguments)
 * @method static Html optgroup(mixed ...$arguments)
 * @method static Html textarea(mixed ...$arguments)
 * @method static Html fieldset(mixed ...$arguments)
 * @method static Html hgroup(mixed ...$arguments)
 * @method static Html h6(mixed ...$arguments)
 * @method static Html h5(mixed ...$arguments)
 * @method static Html h4(mixed ...$arguments)
 * @method static Html h3(mixed ...$arguments)
 * @method static Html h2(mixed ...$arguments)
 * @method static Html h1(mixed ...$arguments)
 * @method static Html p(mixed ...$arguments)
 * @method static Html style(mixed ...$arguments)
 * @method static Html script(mixed ...$arguments)
 * @method static Html noscript(mixed ...$arguments)
 * @method static Html link(mixed ...$arguments)
 * @method static Html meta(mixed ...$arguments)
 * @method static Html html(mixed ...$arguments)
 * @method static Html body(mixed ...$arguments)
 * @method static Html iframe(mixed ...$arguments)
 * @method static Html embed(mixed ...$arguments)
 * @method static Html object(mixed ...$arguments)
 * @method static Html aside(mixed ...$arguments)
 * @method static Html details(mixed ...$arguments)
 * @method static Html figcaption(mixed ...$arguments)
 * @method static Html figure(mixed ...$arguments)
 * @method static Html picture(mixed ...$arguments)
 * @method static Html map(mixed ...$arguments)
 * @method static Html caption(mixed ...$arguments)
 * @method static Html mark(mixed ...$arguments)
 * @method static Html summary(mixed ...$arguments)
 * @method static Html time(mixed ...$arguments)
 * @method static Html blockquote(mixed ...$arguments)
 * @method static Html cite(mixed ...$arguments)
 * @method static Html dialog(mixed ...$arguments)
 * @method static Html table(mixed ...$arguments)
 * @method static Html tr(mixed ...$arguments)
 * @method static Html td(mixed ...$arguments)
 * @method static Html tbody(mixed ...$arguments)
 * @method static Html tfoot(mixed ...$arguments)
 * @method static Html thead(mixed ...$arguments)
 * @method static Html th(mixed ...$arguments)
 * @method static Html video(mixed ...$arguments)
 * @method static Html a(mixed ...$arguments)
 * @method static Html form(mixed ...$arguments)
 * @method static Html img(mixed ...$arguments)
 * @method static Html input(mixed ...$arguments)
 * @method static Html dfn(mixed ...$arguments)
 * @method static Html kbd(mixed ...$arguments)
 * @method static Html q(mixed ...$arguments)
 * @method static Html s(mixed ...$arguments)
 * @method static Html samp(mixed ...$arguments)
 * @method static Html small(mixed ...$arguments)
 * @method static Html u(mixed ...$arguments)
 * @method static Html var(mixed ...$arguments)
 * @method static Html wbr(mixed ...$arguments)
 * @method static Html track(mixed ...$arguments)
 * @method static Html portal(mixed ...$arguments)
 * @method static Html svg(mixed ...$arguments)
 * @method static Html math(mixed ...$arguments)
 * @method static Html group(mixed ...$arguments)
 * @method static Html path(mixed ...$arguments)
 * @method static Html del(mixed ...$arguments)
 * @method static Html ins(mixed ...$arguments)
 * @method static Html progress(mixed ...$arguments)
 * @method static Html slot(mixed ...$arguments)
 * @method static Html template(mixed ...$arguments)
 */
class Html
{
    use Attributes;

    protected string $name;
    protected array $nest = [];
    protected bool $closed = false;

    /**
     * Html constructor.
     *
     * @param string $tag
     * @param string|Html|array|null $arguments
     */
    public function __construct(string $tag, string|Html|array|null ...$arguments)
    {
        $this->name = $tag;

        if( in_array($this->name, ['img', 'br', 'hr', 'input']) ) {
            $this->closed = true;
        }

        foreach ($arguments as $argument) {
            if(is_array($argument) && Arr::isAssociative($argument)) {
                $this->attrExtend($argument);
            } else {
                $this->nest($argument);
            }
        }
    }

    public function isClosed() : bool
    {
        return $this->closed;
    }

    public function tagName() : bool
    {
        return $this->name;
    }

    public function getNest(?int $index = null) : mixed
    {
        return $index ? $this->nest[$index] : $this->nest;
    }

    /**
     * Append Inner Tag
     *
     * @param null|string|Html|array $tag
     *
     * @return $this
     */
    public function nest(null|string|Html|array $tag): static
    {
        if(is_array($tag)) {
            foreach ($tag as $t) {
                $this->nest[] = $t;
            }
        } elseif(isset($tag)) {
            $this->nest[] = $tag;
        }

        return $this;
    }

    /**
     * Prepend inner tag
     *
     * @param Html|string|array $tag
     *
     * @return $this
     */
    public function nestAtTop(Html|string|array $tag ): static
    {
        if(is_array($tag)) {
            foreach ($tag as $t) {
                array_unshift($this->nest, $t);
            }
        } else {
            array_unshift($this->nest, $tag);
        }

        return $this;
    }

    /**
     * Get the opening tag in string form
     *
     * @return string
     */
    public function open(): string
    {
        $openTag = "<{$this->name}";

        foreach($this->attr as $attribute => $value) {
            $value = esc_attr($value);
            $value = $value !== '' ? "=\"{$value}\"" : '';
            $openTag .= " {$attribute}{$value}";
        }

        $openTag .= $this->closed ? " />" : ">";

        return $openTag;
    }

    /**
     * Get the closing tag as string
     *
     * @return string
     */
    public function close(): string
    {
        return $this->closed ? '' : "</{$this->name}>";
    }

    /**
     * Get the string with inner HTML
     *
     * @return string
     */
    public function inner(): string
    {
        $html = '';

        if( ! $this->closed ) {
            foreach($this->nest as $tag) {
                $html .= (string) $tag;
            }
        }

        return $html;
    }

    /**
     * @param string $tag
     * @param null|array $attributes
     * @param string|Html|array $nest
     *
     * @return static
     */
    public static function new(string $tag, $attributes = null, $nest = null): static
    {
        return new static(...func_get_args());
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getString();
    }

    /**
     * Get string
     *
     * @return string
     */
    public function getString(): string
    {
        return $this->open().$this->inner().$this->close();
    }

    /**
     * @param $name
     * @param $arguments
     * @return static
     */
    public static function __callStatic($name, $arguments) : static
    {
        return new static(preg_match('/[A-Z]/', $name) ? Str::dash($name) : $name, ...$arguments);
    }
}
