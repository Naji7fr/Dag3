"""Generate Kniploket Tiko ERD draw.io file for klant-related tables."""

from pathlib import Path
import html
import uuid

OUTPUT = Path(__file__).resolve().parent / "kniploket_klant_erd.drawio"

TABLE_STYLE = (
    "shape=table;startSize=30;container=1;collapsible=1;childLayout=tableLayout;"
    "fixedRows=1;rowLines=0;fontStyle=1;align=center;resizeLast=1;"
)
ROW_STYLE = (
    "shape=partialRectangle;collapsible=0;dropTarget=0;pointerEvents=0;"
    "fillColor=none;points=[[0,0.5],[1,0.5]];portConstraint=eastwest;"
    "top=0;left=0;right=0;bottom=0;"
)
PK_ROW_STYLE = ROW_STYLE.replace("bottom=0", "bottom=1")
KEY_STYLE = "shape=partialRectangle;overflow=hidden;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=0;"
PK_KEY_STYLE = KEY_STYLE.replace("right=0;", "right=0;fontStyle=1;")
PK_NAME_STYLE = KEY_STYLE.replace("right=0;", "right=0;align=left;spacingLeft=6;fontStyle=5;")
FK_KEY_STYLE = KEY_STYLE
NAME_STYLE = KEY_STYLE.replace("right=0;", "right=0;align=left;spacingLeft=6;")

EDGE_STYLE = (
    "edgeStyle=orthogonalEdgeStyle;rounded=0;orthogonalLoop=1;jettySize=auto;html=1;"
    "startArrow=ERmandOne;startFill=0;endArrow=ERoneToMany;endFill=0;"
)
EDGE_ONE_ONE = EDGE_STYLE.replace("endArrow=ERoneToMany", "endArrow=ERmandOne")

TABLES = {
    "users": {
        "x": 320,
        "y": 40,
        "width": 200,
        "fields": [
            ("PK", "id", True),
            ("", "name", False),
            ("UK", "email", False),
            ("", "password", False),
            ("", "role", False),
            ("", "IsActief", False),
        ],
    },
    "Klant": {
        "x": 320,
        "y": 280,
        "width": 200,
        "fields": [
            ("PK", "Id", True),
            ("FK", "UserId", False),
            ("", "Voornaam", False),
            ("", "Tussenvoegsel", False),
            ("", "Achternaam", False),
            ("UK", "Relatienummer", False),
            ("", "Bijzonderheden", False),
            ("", "IsActief", False),
        ],
    },
    "KlantPerContact": {
        "x": 320,
        "y": 560,
        "width": 210,
        "fields": [
            ("PK", "Id", True),
            ("FK", "KlantId", False),
            ("FK", "ContactId", False),
            ("", "IsActief", False),
        ],
    },
    "Contact": {
        "x": 620,
        "y": 560,
        "width": 200,
        "fields": [
            ("PK", "Id", True),
            ("", "Straatnaam", False),
            ("", "Huisnummer", False),
            ("", "Toevoeging", False),
            ("", "Postcode", False),
            ("", "Plaats", False),
            ("", "Email", False),
            ("", "Mobiel", False),
            ("", "IsActief", False),
        ],
    },
}

RELATIONSHIPS = [
    ("users", "Klant", EDGE_ONE_ONE, "UserId"),
    ("Klant", "KlantPerContact", EDGE_STYLE, "KlantId"),
    ("Contact", "KlantPerContact", EDGE_STYLE, "ContactId"),
]


def uid(prefix: str) -> str:
    return f"{prefix}-{uuid.uuid4().hex[:8]}"


class DrawioBuilder:
    def __init__(self) -> None:
        self.cells: list[str] = []
        self.table_ids: dict[str, str] = {}
        self.field_row_ids: dict[tuple[str, str], str] = {}

    def add_table(self, name: str, config: dict) -> None:
        table_id = uid("tbl")
        self.table_ids[name] = table_id
        row_height = 30
        height = 30 + len(config["fields"]) * row_height
        width = config["width"]

        self.cells.append(
            f'        <mxCell id="{table_id}" parent="1" value="{html.escape(name)}" '
            f'style="{TABLE_STYLE}" vertex="1">\n'
            f'          <mxGeometry x="{config["x"]}" y="{config["y"]}" width="{width}" '
            f'height="{height}" as="geometry" />\n'
            f"        </mxCell>"
        )

        for index, (key_type, field_name, is_pk) in enumerate(config["fields"]):
            row_id = uid("row")
            key_id = uid("key")
            name_id = uid("name")
            self.field_row_ids[(name, field_name)] = row_id

            row_style = PK_ROW_STYLE if is_pk else ROW_STYLE
            y = 30 + index * row_height
            name_col_width = width - 30

            self.cells.append(
                f'        <mxCell id="{row_id}" parent="{table_id}" value="" style="{row_style}" vertex="1">\n'
                f'          <mxGeometry y="{y}" width="{width}" height="{row_height}" as="geometry" />\n'
                f"        </mxCell>"
            )
            self.cells.append(
                f'        <mxCell id="{key_id}" parent="{row_id}" value="{html.escape(key_type)}" '
                f'style="{PK_KEY_STYLE if is_pk else FK_KEY_STYLE if key_type == "FK" else KEY_STYLE}" vertex="1">\n'
                f'          <mxGeometry width="30" height="{row_height}" as="geometry">\n'
                f'            <mxRectangle width="30" height="{row_height}" as="alternateBounds" />\n'
                f"          </mxGeometry>\n"
                f"        </mxCell>"
            )
            name_style = PK_NAME_STYLE if is_pk else NAME_STYLE
            self.cells.append(
                f'        <mxCell id="{name_id}" parent="{row_id}" value="{html.escape(field_name)}" '
                f'style="{name_style}" vertex="1">\n'
                f'          <mxGeometry x="30" width="{name_col_width}" height="{row_height}" as="geometry">\n'
                f'            <mxRectangle width="{name_col_width}" height="{row_height}" as="alternateBounds" />\n'
                f"          </mxGeometry>\n"
                f"        </mxCell>"
            )

    def add_edge(self, parent_table: str, child_table: str, style: str, fk_field: str) -> None:
        edge_id = uid("edge")
        parent_pk = "id" if parent_table == "users" else "Id"
        source_id = self.field_row_ids[(parent_table, parent_pk)]
        target_id = self.field_row_ids[(child_table, fk_field)]
        self.cells.append(
            f'        <mxCell id="{edge_id}" parent="1" source="{source_id}" target="{target_id}" '
            f'style="{style}" edge="1">\n'
            f'          <mxGeometry relative="1" as="geometry" />\n'
            f"        </mxCell>"
        )

    def build(self) -> str:
        title_id = uid("title")
        title = (
            f'        <mxCell id="{title_id}" parent="1" value="ERD — Klant, users, Contact, KlantPerContact" '
            f'style="text;html=1;strokeColor=none;fillColor=none;align=center;verticalAlign=middle;'
            f'whiteSpace=wrap;rounded=0;fontSize=16;fontStyle=1;" vertex="1">\n'
            f'          <mxGeometry x="220" y="0" width="500" height="30" as="geometry" />\n'
            f"        </mxCell>"
        )

        return (
            '<mxfile host="app.diagrams.net" modified="2026-07-03T00:00:00.000Z" agent="Kniploket Tiko" version="26.0.0">\n'
            '  <diagram id="kniploket-klant-erd" name="Klant ERD">\n'
            '    <mxGraphModel dx="1200" dy="800" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" '
            'arrows="1" fold="1" page="1" pageScale="1" pageWidth="1169" pageHeight="827" math="0" shadow="0">\n'
            "      <root>\n"
            '        <mxCell id="0" />\n'
            '        <mxCell id="1" parent="0" />\n'
            f"{title}\n"
            + "\n".join(self.cells)
            + "\n      </root>\n"
            "    </mxGraphModel>\n"
            "  </diagram>\n"
            "</mxfile>\n"
        )


def main() -> None:
    builder = DrawioBuilder()
    for name, config in TABLES.items():
        builder.add_table(name, config)
    for source, target, style, fk in RELATIONSHIPS:
        builder.add_edge(source, target, style, fk)

    OUTPUT.write_text(builder.build(), encoding="utf-8")
    print(f"Saved: {OUTPUT}")


if __name__ == "__main__":
    main()
