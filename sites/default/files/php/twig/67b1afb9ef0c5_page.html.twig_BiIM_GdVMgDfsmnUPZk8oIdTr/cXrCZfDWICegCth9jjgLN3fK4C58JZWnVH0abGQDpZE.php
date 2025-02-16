<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* themes/custom/templates/page/page.html.twig */
class __TwigTemplate_86349e1561f0448422d2db1672579029 extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->extensions[SandboxExtension::class];
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 7
        $context["has_breadcrumb"] = "";
        // line 8
        yield "<div class=\"body-page gva-body-page\">
\t";
        // line 9
        yield from $this->loadTemplate((($context["directory"] ?? null) . "/templates/page/parts/preloader.html.twig"), "themes/custom/templates/page/page.html.twig", 9)->unwrap()->yield($context);
        // line 10
        yield "   ";
        yield from $this->loadTemplate(($context["header_skin"] ?? null), "themes/custom/templates/page/page.html.twig", 10)->unwrap()->yield($context);
        // line 11
        yield "\t
   ";
        // line 12
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "breadcrumbs", [], "any", false, false, true, 12)) {
            // line 13
            yield "   \t";
            $context["has_breadcrumb"] = " has-breadcrumb";
            // line 14
            yield "\t\t<div class=\"breadcrumbs\">
\t\t\t";
            // line 15
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "breadcrumbs", [], "any", false, false, true, 15), "html", null, true);
            yield "
\t\t</div>
\t";
        }
        // line 18
        yield "\t
\t<div role=\"main\" class=\"main main-page";
        // line 19
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["has_breadcrumb"] ?? null), "html", null, true);
        yield "\">
\t
\t\t<div class=\"clearfix\"></div>
\t\t";
        // line 22
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "slideshow_content", [], "any", false, false, true, 22)) {
            // line 23
            yield "\t\t\t<div class=\"slideshow_content area\">
\t\t\t\t";
            // line 24
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "slideshow_content", [], "any", false, false, true, 24), "html", null, true);
            yield "
\t\t\t</div>
\t\t";
        }
        // line 26
        yield "\t

\t\t";
        // line 28
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "help", [], "any", false, false, true, 28)) {
            // line 29
            yield "\t\t\t<div class=\"help show hidden\">
\t\t\t\t<div class=\"container\">
\t\t\t\t\t<div class=\"content-inner\">
\t\t\t\t\t\t";
            // line 32
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "help", [], "any", false, false, true, 32), "html", null, true);
            yield "
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t";
        }
        // line 37
        yield "
\t\t";
        // line 38
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "fw_before_content", [], "any", false, false, true, 38)) {
            // line 39
            yield "\t\t\t<div class=\"fw-before-content area\">
\t\t\t\t";
            // line 40
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "fw_before_content", [], "any", false, false, true, 40), "html", null, true);
            yield "
\t\t\t</div>
\t\t";
        }
        // line 43
        yield "\t\t
\t\t<div class=\"clearfix\"></div>
\t\t";
        // line 45
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "before_content", [], "any", false, false, true, 45)) {
            // line 46
            yield "\t\t\t<div class=\"before_content area\">
\t\t\t\t<div class=\"container\">
\t\t\t\t\t<div class=\"row\">
\t\t\t\t\t\t<div class=\"col-xs-12\">
\t\t\t\t\t\t\t";
            // line 50
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "before_content", [], "any", false, false, true, 50), "html", null, true);
            yield "
\t\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t";
        }
        // line 56
        yield "\t\t
\t\t<div class=\"clearfix\"></div>
\t\t
\t\t<div id=\"content\" class=\"content content-full\">
\t\t\t<div class=\"container container-bg\">
\t\t\t\t";
        // line 61
        yield from $this->loadTemplate((($context["directory"] ?? null) . "/templates/page/main.html.twig"), "themes/custom/templates/page/page.html.twig", 61)->unwrap()->yield($context);
        // line 62
        yield "\t\t\t</div>
\t\t</div>

\t\t";
        // line 65
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "highlighted", [], "any", false, false, true, 65)) {
            // line 66
            yield "\t\t\t<div class=\"highlighted area\">
\t\t\t\t<div class=\"container\">
\t\t\t\t\t";
            // line 68
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "highlighted", [], "any", false, false, true, 68), "html", null, true);
            yield "
\t\t\t\t</div>
\t\t\t</div>
\t\t";
        }
        // line 72
        yield "
\t\t";
        // line 73
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "after_content", [], "any", false, false, true, 73)) {
            // line 74
            yield "\t\t\t<div class=\"area after_content\">
\t\t\t\t<div class=\"container-fw\">
\t          \t<div class=\"content-inner\">
\t\t\t\t\t\t ";
            // line 77
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "after_content", [], "any", false, false, true, 77), "html", null, true);
            yield "
\t          \t</div>
        \t\t</div>
\t\t\t</div>
\t\t";
        }
        // line 82
        yield "\t\t
\t\t";
        // line 83
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "fw_after_content", [], "any", false, false, true, 83)) {
            // line 84
            yield "\t\t\t<div class=\"fw-before-content area\">
\t\t\t\t";
            // line 85
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "fw_after_content", [], "any", false, false, true, 85), "html", null, true);
            yield "
\t\t\t</div>
\t\t";
        }
        // line 88
        yield "
\t</div>
</div>

";
        // line 92
        yield from $this->loadTemplate((($context["directory"] ?? null) . "/templates/page/footer.html.twig"), "themes/custom/templates/page/page.html.twig", 92)->unwrap()->yield($context);
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["directory", "header_skin", "page"]);        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "themes/custom/templates/page/page.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  210 => 92,  204 => 88,  198 => 85,  195 => 84,  193 => 83,  190 => 82,  182 => 77,  177 => 74,  175 => 73,  172 => 72,  165 => 68,  161 => 66,  159 => 65,  154 => 62,  152 => 61,  145 => 56,  136 => 50,  130 => 46,  128 => 45,  124 => 43,  118 => 40,  115 => 39,  113 => 38,  110 => 37,  102 => 32,  97 => 29,  95 => 28,  91 => 26,  85 => 24,  82 => 23,  80 => 22,  74 => 19,  71 => 18,  65 => 15,  62 => 14,  59 => 13,  57 => 12,  54 => 11,  51 => 10,  49 => 9,  46 => 8,  44 => 7,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "themes/custom/templates/page/page.html.twig", "D:\\xampp\\htdocs\\funobotz\\themes\\custom\\templates\\page\\page.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("set" => 7, "include" => 9, "if" => 12);
        static $filters = array("escape" => 15);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['set', 'include', 'if'],
                ['escape'],
                [],
                $this->source
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
