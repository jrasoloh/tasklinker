<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\Task;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // On récupère l'objet Project passé en option
        $project = $options['project'];

        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la tâche'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false // La description est facultative [cite: 138]
            ])
            ->add('deadline', DateType::class, [
                'label' => 'Date',
                'widget' => 'single_text',
                'required' => false // La date est facultative [cite: 140]
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    // Les 3 statuts fixes de l'application [cite: 118]
                    'To Do' => 'To Do',
                    'Doing' => 'Doing',
                    'Done' => 'Done',
                ],
            ])
            ->add('assignedUser', EntityType::class, [
                'label' => 'Membre',
                'class' => User::class,
                'choices' => $project->getUsers(), // ON NE PROPOSE QUE LES MEMBRES DU PROJET [cite: 142]
                'choice_label' => 'firstName', // Affiche le prénom dans le select
                'required' => false, // Facultatif [cite: 142]
                'placeholder' => 'Non assignée', // Texte par défaut
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            'project' => null, // On doit déclarer la nouvelle option 'project'
        ]);

        // On s'assure que 'project' est bien un objet Project
        $resolver->setAllowedTypes('project', Project::class);
    }
}
