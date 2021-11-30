<?php


namespace App\Controller;


// Now we need some classes in our Controller because we need that in our form (for the inputs that we will create)
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


// We need the Request component in order to make a formRequest
use App\Entity\Product;

class ProductController extends AbstractController

{
    #[Route("/", name:"product")]
    public function index(): Response
    {
        // Here we will use getDoctrine to use doctrine and we will select the entity that we want to work with and we used findAll() to bring all the information from it and we will save it inside a variable named products and the type of the result will be an array
        $products = $this->getDoctrine()->getRepository('App:Product')->findAll();
        return $this->render('product/index.html.twig', array('products'=>$products));
// we send the result (the variable that have the result of bringing all info from our database) to the index.html.twig page

    }


    #[Route("/create", name: "product_create")]
    public function create(Request $request): Response
    {
        // Here we create an object from the class that we made
        $product = new Product;

/* Here we will build a form using createFormBuilder and inside this function we will put our object and then we write add then we select the input type then an array to add an attribute that we want in our input field */

        $form = $this->createFormBuilder($product)
        ->add('name', TextType::class, array('attr' => array('class'=> 'form-control', 'style'=>'margin-bottom:15px')))
        ->add('price', NumberType::class, array('attr' => array('class'=> 'form-control', 'style'=>'margin-bottom:15px')))
        ->add('description', TextareaType::class, array('attr' => array('class'=> 'form-control', 'style'=>'margin-bottom:15px')))
        ->add('image', TextType::class, array('attr' => array('class'=> 'form-control', 'style'=>'margin-bottom:15px')))
        ->add('save', SubmitType::class, array('label'=> 'Create Product', 'attr' => array('class'=> 'btn-primary', 'style'=>'margin-bottom:15px')))
        ->getForm();
        $form->handleRequest($request);

        /* Here we have an if statement, if we click submit and if  the form is valid we will take the values from the form and we will save them in the new variables */
        if($form->isSubmitted() && $form->isValid()){
            //fetching data

            // taking the data from the inputs by the name of the inputs then getData() function

            $name = $form['name']->getData();
            $price = $form['price']->getData();
            $description = $form['description']->getData();
            $image = $form['image']->getData();

        /* these functions we bring from our entities, every column have a set function and we put the value that we get from the form */

            $product->setName($name);
            $product->setPrice($price);
            $product->setDescription($description);
            $product->setImage($image);

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
            $this->addFlash(
                    'notice',
                    'Product Added'
                    );
            return $this->redirectToRoute('product');

        }
 /* now to make the form we will add this line form->createView() and now you can see the form in create.html.twig file  */
        return $this->render('product/create.html.twig', array('form' => $form->createView()));
    }


    #[Route("/edit/{id}", name:"product_edit")]
    public function edit(Request $request, $id): Response
    {
        /* Here we have a variable product and it will save the result of this search and it will be one result because we search based on a specific id */
        $product = $this->getDoctrine()->getRepository('App:Product')->find($id);

 
    /* Now when you type createFormBuilder and you will put the variable product the form will be filled of the data that you already set it */
         $form = $this->createFormBuilder($product)
            ->add('name', TextType::class, array('attr' => array('class'=> 'form-control', 'style'=>'margin-botton:15px')))
            ->add('price', NumberType::class, array('attr' => array('class'=> 'form-control', 'style'=>'margin-botton:15px')))
            ->add('description', TextareaType::class, array('attr' => array('class'=> 'form-control', 'style'=>'margin-botton:15px')))
            ->add('image', TextType::class, array('attr' => array('class'=> 'form-control', 'style'=>'margin-botton:15px')))
           
             ->add('save', SubmitType::class, array('label'=> 'Update Product', 'attr' => array('class'=> 'btn-primary', 'style'=>'margin-botton:15px')))
             ->getForm();
        $form->handleRequest($request);
 
 
        if($form->isSubmitted() && $form->isValid()){
            //fetching data
            $name = $form['name']->getData();
            $price = $form['price']->getData();
            $description = $form['description']->getData();
            $image = $form['image']->getData();
          
            $em = $this->getDoctrine()->getManager();
            $product = $em->getRepository('App:product')->find($id);
            $product->setName($name);
            $product->setPrice($price);
            $product->setDescription($description);
            $product->setImage($image);
    
            $em->flush();
            $this->addFlash(
                    'notice',
                    'Product Updated'
                    );
            return $this->redirectToRoute('product');
        }
        return $this->render('product/edit.html.twig', array('product' => $product, 'form' => $form->createView()));  
    }


    #[Route("/details/{productId}", name: "detailsAction")]

    public function showdetailsAction($productId)
    {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($productId);



        /*  return new Response('Details from the product with id ' .$productId.", Product name is ".$product->getName()." and it cost ".$product->getPrice(). "€" ); */
        return $this->render('product/details.html.twig', array("product" => $product));
    }
    #[Route("/delete/{id}", name:"todo_delete")]

    public function delete($id){

        $em = $this->getDoctrine()->getManager();

        $todo = $em->getRepository('App:Product')->find($id);
        $em->remove($todo);
        $em->flush();
        $this->addFlash(
         'notice',
            'Todo Removed'
        );

       

        return $this->redirectToRoute('product');

    }

}
