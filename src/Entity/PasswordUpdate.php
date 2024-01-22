<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as  Assert;
use Rollerworks\Component\PasswordStrength\Validator\Constraints as RollerworksPassword;

class PasswordUpdate
{
    private ?string $oldpassword = null;

    #[RollerworksPassword\PasswordRequirements(
        requireLetters:	true,
        requireCaseDiff: true,
        requireNumbers:	true,
        requireSpecialCharacter: true,
        missingLettersMessage: "Doit contenir au moins une lettre.",
        requireCaseDiffMessage: "Doit inclure des lettres majuscules et minuscules.",
        missingNumbersMessage: "Doit inclure au moins un chiffre.",
        missingSpecialCharacterMessage:	'Doit contenir au moins un caractère spécial.',
    )]
    #[RollerworksPassword\PasswordStrength(
        minLength:8,
        minStrength:4,
        tooShortMessage: "Doit comporter au moins {{length}} caractères.",
        message: 'Trop faible!'
    )]
    private ?string $newpassword = null;

    #[Assert\EqualTo(
        propertyPath:"newpassword",
        message:"Vous n'avez pas correctement confirmé votre mot de passe"
    )]
    #[Assert\NotBlank]
    private ?string $passwordConfirm = null;

    public function getOldpassword(): ?string
    {
        return $this->oldpassword;
    }

    public function setOldpassword(string $oldpassword): static
    {
        $this->oldpassword = $oldpassword;

        return $this;
    }

    public function getNewpassword(): ?string
    {
        return $this->newpassword;
    }

    public function setNewpassword(string $newpassword): static
    {
        $this->newpassword = $newpassword;

        return $this;
    }

    public function getPasswordConfirm(): ?string
    {
        return $this->passwordConfirm;
    }

    public function setPasswordConfirm(string $passwordConfirm): static
    {
        $this->passwordConfirm = $passwordConfirm;

        return $this;
    }
}
