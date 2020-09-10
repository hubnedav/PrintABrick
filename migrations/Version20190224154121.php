<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190224154121 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add colors';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (-1, "0033B2", 0, "Unknown")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (0, "05131D", 0, "Black")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (1, "0055BF", 0, "Blue")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (2, "237841", 0, "Green")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (3, "008F9B", 0, "Dark Turquoise")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (4, "C91A09", 0, "Red")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (5, "C870A0", 0, "Dark Pink")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (6, "583927", 0, "Brown")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (7, "9BA19D", 0, "Light Gray")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (8, "6D6E5C", 0, "Dark Gray")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (9, "B4D2E3", 0, "Light Blue")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (10, "4B9F4A", 0, "Bright Green")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (11, "55A5AF", 0, "Light Turquoise")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (12, "F2705E", 0, "Salmon")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (13, "FC97AC", 0, "Pink")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (14, "F2CD37", 0, "Yellow")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (15, "FFFFFF", 0, "White")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (16, "0033B2", 0, "Inherited")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (17, "C2DAB8", 0, "Light Green")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (18, "FBE696", 0, "Light Yellow")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (19, "E4CD9E", 0, "Tan")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (20, "C9CAE2", 0, "Light Violet")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (21, "D4D5C9", 0, "Glow In Dark Opaque")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (22, "81007B", 0, "Purple")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (23, "2032B0", 0, "Dark Blue-Violet")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (25, "FE8A18", 0, "Orange")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (26, "923978", 0, "Magenta")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (27, "BBE90B", 0, "Lime")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (28, "958A73", 0, "Dark Tan")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (29, "E4ADC8", 0, "Bright Pink")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (30, "AC78BA", 0, "Medium Lavender")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (31, "E1D5ED", 0, "Lavender")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (32, "635F52", 1, "Trans-Black IR Lens")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (33, "0020A0", 1, "Trans-Dark Blue")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (34, "84B68D", 1, "Trans-Green")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (35, "D9E4A7", 1, "Trans-Bright Green")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (36, "C91A09", 1, "Trans-Red")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (38, "FF800D", 1, "Trans-Neon Orange")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (39, "C1DFF0", 1, "Trans Very Light Blue")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (40, "635F52", 1, "Trans-Black")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (41, "AEEFEC", 1, "Trans-Light Blue")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (42, "F8F184", 1, "Trans-Neon Green")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (43, "C1DFF0", 1, "Trans-Very Lt Blue")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (45, "DF6695", 1, "Trans-Dark Pink")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (46, "F5CD2F", 1, "Trans-Yellow")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (47, "FCFCFC", 1, "Trans-Clear")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (52, "A5A5CB", 1, "Trans-Purple")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (54, "DAB000", 1, "Trans-Neon Yellow")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (57, "FF800D", 1, "Trans-Neon Orange")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (60, "645A4C", 0, "Chrome Antique Brass")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (61, "6C96BF", 0, "Chrome Blue")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (62, "3CB371", 0, "Chrome Green")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (63, "AA4D8E", 0, "Chrome Pink")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (64, "1B2A34", 0, "Chrome Black")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (67, "FFFFFF", 1, "Rubber Trans Clear")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (68, "F3CF9B", 0, "Very Light Orange")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (69, "CD6298", 0, "Light Purple")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (70, "582A12", 0, "Reddish Brown")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (71, "A0A5A9", 0, "Light Bluish Gray")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (72, "6C6E68", 0, "Dark Bluish Gray")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (73, "5A93DB", 0, "Medium Blue")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (74, "73DCA1", 0, "Medium Green")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (75, "000000", 0, "Speckle Black-Copper")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (76, "635F61", 0, "Speckle DBGray-Silver")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (77, "FECCCF", 0, "Light Pink")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (78, "F6D7B3", 0, "Light Flesh")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (79, "FFFFFF", 0, "Milky White")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (80, "A5A9B4", 0, "Metallic Silver")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (81, "899B5F", 0, "Metallic Green")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (82, "DBAC34", 0, "Metallic Gold")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (84, "CC702A", 0, "Medium Dark Flesh")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (85, "3F3691", 0, "Dark Purple")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (86, "7C503A", 0, "Dark Flesh")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (89, "4C61DB", 0, "Royal Blue")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (92, "D09168", 0, "Flesh")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (100, "FEBABD", 0, "Light Salmon")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (110, "4354A3", 0, "Violet")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (112, "6874CA", 0, "Blue-Violet")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (114, "DF6695", 1, "Glitter Trans-Dark Pink")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (115, "C7D23C", 0, "Medium Lime")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (117, "FFFFFF", 1, "Glitter Trans-Clear")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (118, "B3D7D1", 0, "Aqua")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (120, "D9E4A7", 0, "Light Lime")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (125, "F9BA61", 0, "Light Orange")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (129, "A5A5CB", 1, "Glitter Trans-Purple")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (132, "000000", 0, "Speckle Black-Silver")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (133, "000000", 0, "Speckle Black-Gold")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (134, "AE7A59", 0, "Copper")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (135, "9CA3A8", 0, "Pearl Light Gray")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (137, "7988A1", 0, "Metal Blue")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (142, "DCBC81", 0, "Pearl Light Gold")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (143, "CFE2F7", 1, "Trans-Medium Blue")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (148, "575857", 0, "Pearl Dark Gray")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (150, "ABADAC", 0, "Pearl Very Light Gray")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (151, "E6E3E0", 0, "Very Light Bluish Gray")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (158, "DFEEA5", 0, "Yellowish Green")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (178, "B48455", 0, "Flat Dark Gold")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (179, "898788", 0, "Flat Silver")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (182, "F08F1C", 1, "Trans-Orange")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (183, "F2F3F2", 0, "Pearl White")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (191, "F8BB3D", 0, "Bright Light Orange")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (212, "9FC3E9", 0, "Bright Light Blue")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (216, "B31004", 0, "Rust")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (226, "FFF03A", 0, "Bright Light Yellow")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (230, "E4ADC8", 1, "Trans-Pink")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (232, "7DBFDD", 0, "Sky Blue")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (236, "96709F", 1, "Trans-Light Purple")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (256, "212121", 0, "Rubber Black")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (272, "0A3463", 0, "Dark Blue")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (288, "184632", 0, "Dark Green")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (294, "BDC6AD", 1, "Glow In Dark Trans")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (297, "AA7F2E", 0, "Pearl Gold")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (308, "352100", 0, "Dark Brown")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (313, "3592C3", 0, "Maersk Blue")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (320, "720E0F", 0, "Dark Red")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (321, "078BC9", 0, "Dark Azure")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (322, "36AEBF", 0, "Medium Azure")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (323, "ADC3C0", 0, "Light Aqua")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (324, "C40026", 0, "Rubber Red")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (326, "9B9A5A", 0, "Olive Green")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (334, "BBA53D", 0, "Chrome Gold")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (335, "D67572", 0, "Sand Red")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (351, "F785B1", 0, "Medium Dark Pink")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (366, "FA9C1C", 0, "Earth Orange")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (373, "845E84", 0, "Sand Purple")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (375, "C1C2C1", 0, "Rubber Light Gray")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (378, "A0BCAC", 0, "Sand Green")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (379, "6074A1", 0, "Sand Blue")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (383, "E0E0E0", 0, "Chrome Silver")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (450, "B67B50", 0, "Fabuland Brown")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (462, "FFA70B", 0, "Medium Orange")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (484, "A95500", 0, "Dark Orange")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (493, "656761", 0, "Magnet")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (494, "D0D0D0", 0, "Electric Contact Alloy")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (503, "E6E3DA", 0, "Very Light Gray")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (1000, "D9D9D9", 0, "Glow in Dark White")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (1001, "9391E4", 0, "Medium Violet")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (1002, "C0F500", 1, "Glitter Trans-Neon Green")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (1003, "68BCC5", 1, "Glitter Trans-Light Blue")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (1004, "FCB76D", 1, "Trans Flame Yellowish Orange")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (1005, "FBE890", 1, "Trans Fire Yellow")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (1006, "B4D4F7", 1, "Trans Light Royal Blue")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (1007, "8E5597", 0, "Reddish Lilac")');
        $this->addSql('INSERT INTO color (id, rgb, transparent, name) VALUES (9999, "05131D", 0, "[No Color]")');

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
