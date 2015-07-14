<?php

namespace Models\CMS\Routes;

class Builder
{
    protected $prettyPrinter;
    
    protected $outputPath;
    
    protected $rules = array();
    protected $rulesGrouped = array();

    protected $syntaxTree = array();

    /* */
    
    public function __construct()
    {
        $this->prettyPrinter = new \PHPParser_PrettyPrinter_Default;
    }
    
    /* */
    
    public function setOutputPath($path)
    {
        $this->outputPath = $path;
    }
    
    /* */
    
    public function buildSyntaxTree()
    {
        // Generate grouped roups.
        
        foreach ($this->getGroupedRules() as $group => $rules) {
            $route_statements = array();

            foreach ($rules as $rule) {
                $route_statements[] = new \PHPParser_Node_Expr_MethodCall(
                    new \PHPParser_Node_Expr_StaticCall(
                        new \PHPParser_Node_Name('Route'),
                        'any',
                        array(
                            new \PHPParser_Node_Arg(
                                new \PHPParser_Node_Scalar_String('{'.strtr($rule->getURL(), '-', '_').'}')
                            ),
                            new \PHPParser_Node_Arg(
                                new \PHPParser_Node_Scalar_String($rule->getController())
                            ),
                        )
                    ),
                    'where',
                    array(
                        new \PHPParser_Node_Arg(
                            new \PHPParser_Node_Scalar_String(strtr($rule->getURL(), '-', '_'))
                        ),
                        new \PHPParser_Node_Arg(
                            new \PHPParser_Node_Scalar_String($rule->getRegex())
                        ),
                    )
                );
            }
            
            $this->syntaxTree[] = new \PHPParser_Node_Expr_StaticCall(
                new \PHPParser_Node_Name(
                    'Route'
                ),
                'group',
                array(
                    new \PHPParser_Node_Arg(
                        new \PHPParser_Node_Expr_Array(
                            array(
                                new \PHPParser_Node_Expr_ArrayItem(
                                    new \PHPParser_Node_Scalar_String($group),
                                    new \PHPParser_Node_Scalar_String('prefix')
                                ),
                            )
                        )
                    ),
                    new \PHPParser_Node_Arg(
                        new \PHPParser_Node_Expr_Closure(
                            array(
                                'stmts' => $route_statements,
                            )
                        )
                    )
                )
            );
        }
        
        // Generate individual route statements.

        foreach ($this->getRules() as $rule) {
            $this->syntaxTree[] = new \PHPParser_Node_Expr_MethodCall(
                new \PHPParser_Node_Expr_StaticCall(
                    new \PHPParser_Node_Name('Route'),
                    'any',
                    array(
                        new \PHPParser_Node_Arg(
                            new \PHPParser_Node_Scalar_String('{'.strtr($rule->getURL(), '-', '_').'}')
                        ),
                        new \PHPParser_Node_Arg(
                            new \PHPParser_Node_Scalar_String($rule->getController())
                        ),
                    )
                ),
                'where',
                array(
                    new \PHPParser_Node_Arg(
                        new \PHPParser_Node_Scalar_String(strtr($rule->getURL(), '-', '_'))
                    ),
                    new \PHPParser_Node_Arg(
                        new \PHPParser_Node_Scalar_String($rule->getRegex())
                    ),
                )
            );
        }
    }

    /* */

    public function addRule($group, $url, $controller, $regex = '')
    {
        $url = trim($url, ' \\/');
        
        if (!$regex) {
            $regex = preg_quote($url).'(/.*)?';
        }
        
        //
        
        $rule = new Rule($url, $controller, $regex);
        
        if ($group) {
            if(!isset($this->rulesGrouped[$group])) {
                $this->rulesGrouped[$group] = array();
            }
            
            $this->rulesGrouped[$group][] = $rule;
        } else {
            $this->rules[] = $rule;
        }
    }
    
    public function getRules()
    {
        return $this->rules;
    }
    
    public function getGroupedRules()
    {
        return $this->rulesGrouped;
    }
    
    /* */
    
    public function compileRoutes()
    {
        $this->buildSyntaxTree();

        file_put_contents($this->outputPath, "<?php\n\n/*\n *\n * WARNING: This code is generated automatically; changes to this file may be lost.\n *\n */\n\n".$this->prettyPrinter->prettyPrint($this->syntaxTree));

        return $this;
    }
}